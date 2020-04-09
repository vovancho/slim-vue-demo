<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task\Listener\Notification;

use App\TaskHandler\Channel\NotificationChannel;
use App\TaskHandler\Entity\Task\Event\TaskProcessed;
use PhpAmqpLib\Message\AMQPMessage;

class ProcessedListener
{
    private NotificationChannel $channel;

    public function __construct(NotificationChannel $channel)
    {
        $this->channel = $channel;
    }

    public function __invoke(TaskProcessed $event)
    {
        $data = [
            'event' => TaskProcessed::class,
            'author_id' => $event->author->getId()->getValue(),
            'visibility' => $event->visibility->getName(),
            'process_percent' => $event->task->getProcessPercent(),
            'task_id' => $event->task->getId()->getValue(),
        ];

        $message = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            ['content_type' => 'text/plain']
        );

        $this->channel->basicPublish($message, NotificationChannel::EXCHANGE);
    }
}
