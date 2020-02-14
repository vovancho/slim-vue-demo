<?php

declare(strict_types=1);

namespace Api\Test\Feature\Task\Cancel;


use Api\Test\Feature\AuthFixture;
use Api\Test\Feature\WebTestCase;

class SuccessTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->loadFixtures([
            'auth' => AuthFixture::class,
            'task' => TaskFixture::class,
        ]);

        parent::setUp();
    }

    public function testGuest(): void
    {
        $task = $this->getTask();
        $taskId = $task->getTask()->getId()->getId();

        $response = $this->get("/tasks/$taskId/cancel");
        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $auth = $this->getAuth();
        $task = $this->getTask();
        $taskId = $task->getTask()->getId()->getId();

        $response = $this->delete("/tasks/$taskId/cancel", [], $auth->getHeaders());

        self::assertEquals(204, $response->getStatusCode());
    }

    private function getAuth(): AuthFixture
    {
        return $this->getFixture('auth');
    }

    private function getTask(): TaskFixture
    {
        return $this->getFixture('task');
    }
}
