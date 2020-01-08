<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\Base\Uuid1;

class TaskExecuted
{
    public $id;

    public function __construct(Uuid1 $id)
    {
        $this->id = $id;
    }
}
