<?php

declare(strict_types=1);

namespace App\Test\Functional\TaskHandler\Service\Processor\TaskProcessor;

use App\Flusher;
use App\TaskHandler\Entity\Task\Status;
use App\TaskHandler\Entity\Task\TaskRepository;
use App\TaskHandler\Service\Processor\TaskProcessor;
use App\Test\Functional\V1\Auth\OAuth\AuthFixture;
use App\Test\Functional\V1\TaskHandler\Cancel\TaskFixture;
use App\Test\Functional\WebTestCase;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\TaskHandler\Service\Processor\TaskProcessor
 */
class ExecuteTest extends WebTestCase
{
    protected Flusher $flusher;
    protected MockObject $logger;
    protected TaskRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var ContainerInterface $container */
        $container = $this->app()->getContainer();

        $this->repository = $container->get(TaskRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->flusher = $container->get(Flusher::class);

        $this->loadFixtures([
            'auth' => AuthFixture::class,
            'task' => TaskFixture::class,
        ]);
    }

    public function testExecute(): void
    {
        $this->logger->expects($this->never())->method('warning');

        $processor = new class ($this->repository, $this->flusher, $this->logger) extends TaskProcessor
        {
            public function process(): Generator
            {
                yield 20;
            }
        };

        $task = $this->getTask()->getTask();

        $id = $task->getId();
        $name = $task->getName();
        $author = $task->getAuthor();
        $visibility = $task->getVisibility();
        $pushedAt = $task->getPushedAt();

        $processor->run($id);

        $task = $this->repository->get($id);

        self::assertEquals($id, $task->getId());
        self::assertEquals($name, $task->getName());
        self::assertEquals(Status::execute(), $task->getStatus());
        self::assertEquals(20, $task->getProcessPercent());
        self::assertEquals($author, $task->getAuthor());
        self::assertEquals($visibility, $task->getVisibility());
        self::assertNull($task->getError());
        self::assertEquals($pushedAt->format('Y-m-d H:i:s'), $task->getPushedAt()->format('Y-m-d H:i:s'));
    }

    public function testZeroPercent(): void
    {
        $this->logger->expects($this->never())->method('warning');

        $processor = new class ($this->repository, $this->flusher, $this->logger) extends TaskProcessor
        {
            public function process(): Generator
            {
                yield 0;
            }
        };

        $task = $this->getTask()->getTask();

        $id = $task->getId();
        $name = $task->getName();
        $author = $task->getAuthor();
        $visibility = $task->getVisibility();
        $pushedAt = $task->getPushedAt();

        $processor->run($id);

        $task = $this->repository->get($id);

        self::assertEquals($id, $task->getId());
        self::assertEquals($name, $task->getName());
        self::assertEquals(Status::execute(), $task->getStatus());
        self::assertEquals(0, $task->getProcessPercent());
        self::assertEquals($author, $task->getAuthor());
        self::assertEquals($visibility, $task->getVisibility());
        self::assertNull($task->getError());
        self::assertEquals($pushedAt->format('Y-m-d H:i:s'), $task->getPushedAt()->format('Y-m-d H:i:s'));
    }

    private function getTask(): TaskFixture
    {
        return $this->getFixture('task');
    }
}
