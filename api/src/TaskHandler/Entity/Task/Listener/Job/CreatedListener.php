<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task\Listener\Job;

use App\TaskHandler\Channel\JobChannel;
use App\TaskHandler\Entity\Task\Event\TaskCreated;
use PhpAmqpLib\Message\AMQPMessage;

class CreatedListener
{
    private JobChannel $channel;

    public function __construct(JobChannel $channel)
    {
        $this->channel = $channel;
    }

    public function __invoke(TaskCreated $event)
    {
        $data = [
            'id' => $event->id->getValue(),
        ];

        $message = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            ['content_type' => 'text/plain']
        );

        $this->channel->basicPublish($message, JobChannel::EXCHANGE);
    }
}
