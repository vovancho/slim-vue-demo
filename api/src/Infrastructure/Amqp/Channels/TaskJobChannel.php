<?php

declare(strict_types=1);

namespace Api\Infrastructure\Amqp\Channels;


use Api\Infrastructure\Amqp\ChannelInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class TaskJobChannel implements ChannelInterface
{
    const QUEUE = 'tasks.jobs.queue';
    const EXCHANGE = 'tasks.jobs';

    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function basic_publish(AMQPMessage $msg, string $exchange = '', string $routing_key = '', bool $mandatory = false, bool $immediate = false, ?int $ticket = null): void
    {
        $this->channel->basic_publish($msg, $exchange, $routing_key, $mandatory, $immediate, $ticket);
    }

    public function basic_consume(string $queue = '', string $consumer_tag = '', bool $no_local = false, bool $no_ack = false, bool $exclusive = false, bool $nowait = false, ?callable $callback = null, ?int $ticket = null, array $arguments = [])
    {
        return $this->channel->basic_consume($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait, $callback, $ticket, $arguments);
    }

    public function wait(): void
    {
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}
