<?php

declare(strict_types=1);

namespace App\TaskHandler\Channel;

use App\Framework\Amqp\BaseChannel;
use App\Framework\Amqp\ChannelInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class JobChannel extends BaseChannel implements ChannelInterface
{
    public const QUEUE = 'tasks.jobs.queue';
    public const EXCHANGE = 'tasks.jobs';

    public function basicPublish(
        AMQPMessage $msg,
        string $exchange = '',
        string $routing_key = '',
        bool $mandatory = false,
        bool $immediate = false,
        ?int $ticket = null
    ): void {
        $this->getChannel()->basic_publish($msg, $exchange, $routing_key, $mandatory, $immediate, $ticket);
    }

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
    ) {
        return $this->getChannel()->basic_consume(
            $queue,
            $consumer_tag,
            $no_local,
            $no_ack,
            $exclusive,
            $nowait,
            $callback,
            $ticket,
            $arguments
        );
    }

    public function wait(): void
    {
        while ($this->getChannel()->is_consuming()) {
            $this->getChannel()->wait();
        }
    }

    protected function init(AMQPChannel $channel)
    {
        $channel->queue_declare(self::QUEUE, false, true, false, false);
        $channel->exchange_declare(self::EXCHANGE, 'fanout', false, true, false);
        $channel->queue_bind(self::QUEUE, self::EXCHANGE);
    }
}
