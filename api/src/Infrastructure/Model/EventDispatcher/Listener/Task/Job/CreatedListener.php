<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\EventDispatcher\Listener\Task\Job;


use Api\Infrastructure\Amqp\Channels\TaskJobChannel;
use Api\Model\Task\Entity\Task\Event\TaskCreated;
use PhpAmqpLib\Message\AMQPMessage;

class CreatedListener
{
    private $channel;

    public function __construct(TaskJobChannel $channel)
    {
        $this->channel = $channel;
    }

    public function __invoke(TaskCreated $event)
    {
        $data = [
            'id' => $event->id->getId(),
        ];

        $message = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            ['content_type' => 'text/plain']
        );

        $this->channel->basic_publish($message, TaskJobChannel::EXCHANGE);
    }
}
