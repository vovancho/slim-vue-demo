<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use App\Framework\AggregateRoot;
use App\Framework\EventTrait;
use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Task\Event\TaskCanceled;
use App\TaskHandler\Entity\Task\Event\TaskCompleted;
use App\TaskHandler\Entity\Task\Event\TaskCreated;
use App\TaskHandler\Entity\Task\Event\TaskError;
use App\TaskHandler\Entity\Task\Event\TaskExecuted;
use App\TaskHandler\Entity\Task\Event\TaskProcessed;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="task_handler_tasks")
 */
class Task implements AggregateRoot
{
    use EventTrait;

    /**
     * @ORM\Column(type="task_handler_task_id")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $pushedAt;
    /**
     * @ORM\Embedded(class="App\TaskHandler\Entity\Author\Author")
     */
    private Author $author;
    /**
     * @ORM\Column(type="task_handler_task_visibility", length=16)
     */
    private Visibility $visibility;
    /**
     * @ORM\Column(type="string")
     */
    private string $name;
    /**
     * @ORM\Column(type="task_handler_task_status", length=16)
     */
    private Status $status;
    /**
     * @ORM\Column(type="smallint")
     */
    private int $processPercent;
    /**
     * @ORM\Embedded(class="Error")
     */
    private ?Error $error = null;

    public function __construct(
        Id $id,
        DateTimeImmutable $pushedAt,
        Author $author,
        Visibility $visibility,
        string $name
    ) {
        $this->id = $id;
        $this->pushedAt = $pushedAt;
        $this->author = $author;
        $this->visibility = $visibility;
        $this->name = $name;
        $this->status = Status::wait();
        $this->processPercent = 0;
        $this->recordEvent(new TaskCreated($this->getVisibility(), $this->getAuthor(), $this->getId()));
    }

    public function execute(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('Выполнение задачи возможно только в статусе ожидания.');
        }

        $this->status = Status::execute();
        $this->recordEvent(new TaskExecuted($this->getVisibility(), $this->getAuthor()));
    }

    public function addPercent(int $percentAdded)
    {
        if (!$this->isExecute()) {
            throw new DomainException('Изменение хода выполнения возможно только при выполнении задачи.');
        }
        $percent = $this->getProcessPercent();
        $percent += $percentAdded;
        $this->setProcessPercent($percent);
        if ($this->getProcessPercent() < 100) {
            $this->recordEvent(new TaskProcessed($this->getVisibility(), $this->getAuthor(), clone $this));
        }
    }

    public function complete()
    {
        if (!$this->isExecute()) {
            throw new DomainException('Задание может быть выполнено только в статусе выполнения.');
        }

        if ($this->getProcessPercent() < 100) {
            throw new DomainException('Задание не может быть выполнено с процентом выполнения меньше 100.');
        }

        $this->status = Status::complete();
        $this->recordEvent(new TaskCompleted($this->getVisibility(), $this->getAuthor()));
    }

    public function cancel()
    {
        if ($this->isComplete() || $this->isInterrupted()) {
            throw new DomainException('Задание не может быть отменено.');
        }
        $this->status = Status::cancel();
        $this->recordEvent(new TaskCanceled($this->getVisibility(), $this->getAuthor()));
    }

    public function error(\Exception $e)
    {
        if (!$this->isExecute()) {
            throw new DomainException('Ошибка выполнения возможна только при выполнении задачи.');
        }
        $this->status = Status::error();
        $this->setError(new Error($e->getMessage(), $e->getTraceAsString()));
        $this->recordEvent(new TaskError($this->getVisibility(), $this->getAuthor()));
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isExecute()
    {
        return $this->status->isExecute();
    }

    public function isComplete(): bool
    {
        return $this->status->isComplete();
    }

    public function isInterrupted(): bool
    {
        return $this->status->isInterrupted();
    }

    public function isCancel(): bool
    {
        return $this->status->isCancel();
    }

    public function isError(): bool
    {
        return $this->status->isError();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getPushedAt(): DateTimeImmutable
    {
        return $this->pushedAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setProcessPercent(int $percent): void
    {
        $percent = $percent > 100 ? 100 : $percent;
        $percent = $percent < 0 ? 0 : $percent;
        $this->processPercent = $percent;
    }

    public function getProcessPercent(): int
    {
        return $this->processPercent;
    }

    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setError(Error $error): void
    {
        $this->error = $error;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->error && $this->error->isEmpty()) {
            $this->error = null;
        }
    }
}
