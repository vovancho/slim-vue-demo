<?php

declare(strict_types=1);

namespace App\Framework\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class BaseChannel
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    protected function getChannel(): AMQPChannel
    {
        if (empty($this->channel)) {
            $this->channel = $this->connection->channel();

            $this->init($this->channel);

            register_shutdown_function(function (AMQPChannel $channel, AMQPStreamConnection $connection) {
                $channel->close();
                $connection->close();
            }, $this->channel, $this->connection);
        }

        return $this->channel;
    }

    abstract protected function init(AMQPChannel $channel);
}
