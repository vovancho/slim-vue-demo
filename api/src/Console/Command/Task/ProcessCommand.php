<?php

declare(strict_types=1);

namespace Api\Console\Command\Task;


use Api\Infrastructure\Amqp\Channels\TaskJobChannel;
use Api\Model\Base\Uuid1;
use Api\Model\Task\UseCase\Execute\Handler;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Api\Model\Task\UseCase;

class ProcessCommand extends Command
{
    private $channel;
    private $handler;

    public function __construct(TaskJobChannel $channel, Handler $handler)
    {
        parent::__construct();
        $this->channel = $channel;
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this->setName('tasks:process');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Consume messages</comment>');

        $consumerTag = 'consumer_' . getmypid();
        $this->channel->basic_consume(TaskJobChannel::QUEUE, $consumerTag, false, false, false, false, function ($message) use ($output) {


            $body = json_decode($message->body, true);
            $output->writeln(print_r($body, true));

            $command = new UseCase\Execute\Command();
            $command->id = new Uuid1($body['id']);

            $this->handler->handle($command);

            /** @var AMQPChannel $channel */
            $channel = $message->delivery_info['channel'];
            $channel->basic_ack($message->delivery_info['delivery_tag']);
        });

        $this->channel->wait();

        $output->writeln('<info>Done!</info>');
    }
}
