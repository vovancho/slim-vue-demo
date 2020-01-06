<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task;


use Api\Model\AggregateRoot;
use Api\Model\Base\Uuid1;
use Api\Model\EventTrait;
use Api\Model\Task\Entity\Task\Event\TaskCreated;
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
        $this->recordEvent(new TaskCreated($this->id));
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
}
