<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Builder;

use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Author\Email;
use App\TaskHandler\Entity\Task\Id;
use App\TaskHandler\Entity\Task\Task;
use App\TaskHandler\Entity\Task\Visibility;
use DateTimeImmutable;
use Exception;

class TaskBuilder
{
    private Id $id;
    private DateTimeImmutable $pushedAt;
    private Author $author;
    private Visibility $visibility;
    private string $name;
    private bool $execute = false;
    private bool $complete = false;
    private bool $error = false;
    private bool $process = false;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->pushedAt = new DateTimeImmutable();
        $this->author = $this->createAuthor();
        $this->visibility = Visibility::public();
        $this->name = 'Task1';
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withPushedAt(DateTimeImmutable $pushedAt): self
    {
        $clone = clone $this;
        $clone->pushedAt = $pushedAt;
        return $clone;
    }

    public function withAuthor(Author $author): self
    {
        $clone = clone $this;
        $clone->author = $author;
        return $clone;
    }

    public function withVisibility(Visibility $visibility): self
    {
        $clone = clone $this;
        $clone->visibility = $visibility;
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
        $task = new Task(
            $this->id,
            $this->pushedAt,
            $this->author,
            $this->visibility,
            $this->name
        );

        if (
            $this->execute
            || $this->complete
            || $this->error
            || $this->process
        ) {
            $task->execute();
        }

        if ($this->complete) {
            $task->addPercent(100);
            $task->complete();
        } elseif ($this->error) {
            $task->error(new Exception('error'));
        } elseif ($this->process) {
            $task->addPercent(100);
        }

        return $task;
    }

    private function createAuthor(): Author
    {
        return (new AuthorBuilder())
            ->withEmail(new Email('author@example.com'))
            ->build();
    }

    public function execute(): self
    {
        $clone = clone $this;
        $clone->execute = true;
        return $clone;
    }

    public function complete(): self
    {
        $clone = clone $this;
        $clone->complete = true;
        return $clone;
    }

    public function error(): self
    {
        $clone = clone $this;
        $clone->error = true;
        return $clone;
    }

    public function process()
    {
        $clone = clone $this;
        $clone->process = true;
        return $clone;
    }
}
