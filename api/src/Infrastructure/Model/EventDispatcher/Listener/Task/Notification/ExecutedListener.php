<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\EventDispatcher\Listener\Task\Notification;


use Api\Infrastructure\Amqp\Channels\TaskNotificationChannel;
use Api\Model\Task\Entity\Task\Event\TaskExecuted;
use PhpAmqpLib\Message\AMQPMessage;

class ExecutedListener
{
    private $channel;

    public function __construct(TaskNotificationChannel $channel)
    {
        $this->channel = $channel;
    }

    public function __invoke(TaskExecuted $event)
    {
        $data = [
            'event' => TaskExecuted::class,
        ];

        $message = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            ['content_type' => 'text/plain']
        );

        $this->channel->basic_publish($message, TaskNotificationChannel::EXCHANGE);
    }
}
