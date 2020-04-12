<?php

declare(strict_types=1);

namespace App\Console\Command\Task;

use App\Framework\Amqp\ChannelInterface;
use App\TaskHandler\Channel\JobChannel;
use App\TaskHandler\Command\Execute\Handler;
use App\TaskHandler\Command\Execute\Command;
use App\TaskHandler\Entity\Task\Id;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCommand extends ConsoleCommand
{
    private ChannelInterface $channel;
    private Handler $handler;

    public function __construct(JobChannel $channel, Handler $handler)
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
        $consumerTag = 'consumer_' . getmypid();
        $this->channel->basicConsume(
            JobChannel::QUEUE,
            $consumerTag,
            false,
            false,
            false,
            false,
            function ($message) use ($output) {
                $body = json_decode($message->body, true);

                $command = new Command();
                $command->id = new Id($body['id']);

                $this->handler->handle($command);

                /** @var AMQPChannel $channel */
                $channel = $message->delivery_info['channel'];
                $channel->basic_ack($message->delivery_info['delivery_tag']);
            }
        );

        $this->channel->wait();
    }
}
