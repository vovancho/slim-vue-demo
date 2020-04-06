<?php

declare(strict_types=1);

namespace App\Framework\Amqp;

use PhpAmqpLib\Message\AMQPMessage;

interface ChannelInterface
{
    public function basicPublish(
        AMQPMessage $msg,
        string $exchange = '',
        string $routing_key = '',
        bool $mandatory = false,
        bool $immediate = false,
        ?int $ticket = null
    ): void;

    public function basicConsume(
        string $queue = '',
        string $consumer_tag = '',
        bool $no_local = false,
        bool $no_ack = false,
        bool $exclusive = false,
        bool $nowait = false,
        ?callable $callback = null,
        ?int $ticket = null,
        array $arguments = []
    );

    public function wait();
}
