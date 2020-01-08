<?php

declare(strict_types=1);

use Api\Infrastructure\Amqp\Channels\TaskJobChannel;
use Api\Infrastructure\Amqp\Channels\TaskNotificationChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;

return [
    AMQPStreamConnection::class => function (ContainerInterface $container) {
        $config = $container->get('config')['amqp'];
        return new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['username'],
            $config['password'],
            $config['vhost']
        );
    },
    TaskJobChannel::class => function (ContainerInterface $container) {
        /** @var AMQPStreamConnection $connection */
        $connection = $container->get(AMQPStreamConnection::class);

        $channel = $connection->channel();

        $channel->queue_declare(TaskJobChannel::QUEUE, false, false, false, false);
        $channel->exchange_declare(TaskJobChannel::EXCHANGE, 'fanout', false, false, false);
        $channel->queue_bind(TaskJobChannel::QUEUE, TaskJobChannel::EXCHANGE);

        register_shutdown_function(function (AMQPChannel $channel, AMQPStreamConnection $connection) {
            $channel->close();
            $connection->close();
        }, $channel, $connection);

        return new TaskJobChannel($channel);
    },
    TaskNotificationChannel::class => function (ContainerInterface $container) {
        /** @var AMQPStreamConnection $connection */
        $connection = $container->get(AMQPStreamConnection::class);

        $channel = $connection->channel();

        $channel->queue_declare(TaskNotificationChannel::QUEUE, false, false, false, false);
        $channel->exchange_declare(TaskNotificationChannel::EXCHANGE, 'fanout', false, false, false);
        $channel->queue_bind(TaskNotificationChannel::QUEUE, TaskNotificationChannel::EXCHANGE);

        register_shutdown_function(function (AMQPChannel $channel, AMQPStreamConnection $connection) {
            $channel->close();
            $connection->close();
        }, $channel, $connection);

        return new TaskNotificationChannel($channel);
    },

    'config' => [
        'amqp' => [
            'host' => getenv('API_AMQP_HOST'),
            'port' => getenv('API_AMQP_PORT'),
            'username' => getenv('API_AMQP_USERNAME'),
            'password' => getenv('API_AMQP_PASSWORD'),
            'vhost' => getenv('API_AMQP_VHOST'),
        ]
    ]
];