<?php

declare(strict_types=1);

namespace Api\Test\Builder\Task;


use Api\Model\Base\Uuid1;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\User\Entity\User\User;
use Api\Test\Builder\User\UserBuilder;

class TaskBuilder
{
    private $id;
    private $pushedAt;
    private $user;
    private $type;
    private $name;

    public function __construct()
    {
        $this->id = Uuid1::next();
        $this->pushedAt = new \DateTimeImmutable();
        $this->type = Task::TYPE_PUBLIC;
        $this->name = 'Task1';
        $this->user = $this->user();
    }

    public function withId(Uuid1 $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withPushedDate(\DateTimeImmutable $date): self
    {
        $clone = clone $this;
        $clone->pushedAt = $date;
        return $clone;
    }

    public function withUser(User $user): self
    {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function withType(string $type): self
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function build(): Task
    {
        return new Task(
            $this->id,
            $this->pushedAt,
            $this->user,
            $this->type,
            $this->name
        );
    }

    private function user(): User
    {
        return (new UserBuilder())->build();
    }
}
