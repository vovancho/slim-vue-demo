<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task;


use Api\Model\AggregateRoot;
use Api\Model\Base\Uuid1;
use Api\Model\EventTrait;
use Api\Model\Task\Entity\Task\Event\TaskCanceled;
use Api\Model\Task\Entity\Task\Event\TaskCreated;
use Api\Model\Task\Entity\Task\Event\TaskExecuted;
use Api\Model\Task\Entity\Task\Event\TaskProcessed;
use Api\Model\Task\Entity\Task\Event\TaskCompleted;
use Api\Model\Task\Entity\Task\Event\TaskError;
use Api\Model\User\Entity\User\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="task_tasks")
 */
class Task implements AggregateRoot
{
    use EventTrait;

    const STATUS_WAIT = 'wait';
    const STATUS_EXECUTE = 'execute';
    const STATUS_COMPLETE = 'complete';
    const STATUS_CANCEL = 'cancel';
    const STATUS_ERROR = 'error';

    const TYPE_PRIVATE = 'private';
    const TYPE_PUBLIC = 'public';

    /**
     * @var Uuid1
     * @ORM\Column(type="uuid1")
     * @ORM\Id
     */
    private $id;
    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $pushedAt;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Api\Model\User\Entity\User\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;
    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    private $type;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    private $status;
    /**
     * @var integer
     * @ORM\Column(type="smallint")
     */
    private $processPercent;
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $errorMessage;
    /**
     * @var int
     */
    private $position;

    public function __construct(
        Uuid1 $id,
        \DateTimeImmutable $pushedAt,
        User $user,
        string $type,
        string $name
    )
    {
        $this->id = $id;
        $this->pushedAt = $pushedAt;
        $this->user = $user;
        $this->type = $type;
        $this->name = $name;
        $this->status = self::STATUS_WAIT;
        $this->processPercent = 0;
        $this->position = 0;
        $this->recordEvent(new TaskCreated($this->getId(), $this->getType(), $this->getUser()));
    }

    public function execute(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('Выполнение задачи возможно только в статусе ожидания.');
        }

        $this->status = self::STATUS_EXECUTE;
        $this->recordEvent(new TaskExecuted($this->getId()));
    }

    public function addPercent(int $percentAdded)
    {
        if (!$this->isExecute()) {
            throw new \DomainException('Изменение хода выполнения возможно только при выполнении задачи.');
        }
        $percent = $this->getProcessPercent();
        $percent += $percentAdded;
        $this->setProcessPercent($percent);
        if ($this->getProcessPercent() < 100) {
            $this->recordEvent(new TaskProcessed(clone $this));
        }
    }

    public function complete()
    {
        if ($this->getProcessPercent() < 100) {
            throw new \DomainException('Задание не может быть выполнено с процентом выполнения меньше 100.');
        }

        $this->status = self::STATUS_COMPLETE;
        $this->recordEvent(new TaskCompleted(clone $this));
    }

    public function cancel()
    {
        if ($this->isComplete() || $this->isInterrupted()) {
            throw new \DomainException('Задание не может быть отменено.');
        }
        $this->status = self::STATUS_CANCEL;
        $this->recordEvent(new TaskCanceled($this->getId()));
    }

    public function error(\Exception $e)
    {
        if (!$this->isExecute()) {
            throw new \DomainException('Ошибка выполнения возможна только при выполнении задачи.');
        }
        $this->status = self::STATUS_ERROR;
        $json = json_encode([
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        $this->setErrorMessage($json);
        $this->recordEvent(new TaskError(clone $this));
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isExecute()
    {
        return $this->status === self::STATUS_EXECUTE;
    }

    public function isExecuting(): bool
    {
        return !$this->isInterrupted() && ($this->getProcessPercent() < 100 || !$this->isComplete());
    }

    public function isComplete(): bool
    {
        return $this->status === self::STATUS_COMPLETE;
    }

    public function isInterrupted(): bool
    {
        return in_array($this->status, [self::STATUS_CANCEL, self::STATUS_ERROR]);
    }

    public static function getTypes(): array
    {
        return [self::TYPE_PRIVATE, self::TYPE_PUBLIC];
    }

    public function getId(): Uuid1
    {
        return $this->id;
    }

    public function getPushedAt(): \DateTimeImmutable
    {
        return $this->pushedAt;
    }

    public function getName()
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

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
