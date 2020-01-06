<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\EventDispatcher\Listener\Task;


use Api\Infrastructure\Amqp\AMQPHelper;
use Api\Model\Task\Entity\Task\Event\TaskCreated;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CreatedListener
{
    private $connection;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke(TaskCreated $event)
    {
        $connection = $this->connection;

        $channel = $this->connection->channel();

        AMQPHelper::initNotifications($channel);
        AMQPHelper::registerShutdown($connection, $channel);

        $data = [
            'type' => 'notification',
            'task_id' => $event->id,
            'message' => 'Video created',
        ];

        $message = new AMQPMessage(
            json_encode($data),
            ['content_type' => 'text/plain']
        );

        $channel->basic_publish($message, AMQPHelper::EXCHANGE_NOTIFICATIONS);
    }
}
