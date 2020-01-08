<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\EventDispatcher\Listener\Task\Notification;


use Api\Infrastructure\Amqp\Channels\TaskNotificationChannel;
use Api\Model\Task\Entity\Task\Event\TaskCompleted;
use PhpAmqpLib\Message\AMQPMessage;

class CompletedListener
{
    private $channel;

    public function __construct(TaskNotificationChannel $channel)
    {
        $this->channel = $channel;
    }

    public function __invoke(TaskCompleted $event)
    {
        $data = [
            'event' => TaskCompleted::class,
            'task' => [
                'id' => $event->task->getId()->getId(),
                'status' => $event->task->getStatus(),
                'process_percent' => $event->task->getProcessPercent(),
                'error_message' => $event->task->getErrorMessage(),
                'position' => $event->task->getPosition(),
            ],
        ];

        $message = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            ['content_type' => 'text/plain']
        );

        $this->channel->basic_publish($message, TaskNotificationChannel::EXCHANGE);
    }
}
