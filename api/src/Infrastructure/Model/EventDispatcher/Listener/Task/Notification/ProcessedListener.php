<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\EventDispatcher\Listener\Task\Notification;


use Api\Infrastructure\Amqp\Channels\TaskNotificationChannel;
use Api\Model\Task\Entity\Task\Event\TaskProcessed;
use PhpAmqpLib\Message\AMQPMessage;

class ProcessedListener
{
    private $channel;

    public function __construct(TaskNotificationChannel $channel)
    {
        $this->channel = $channel;
    }

    public function __invoke(TaskProcessed $event)
    {
        $data = [
            'event' => TaskProcessed::class,
            'user_id' => $event->user->getId()->getId(),
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
