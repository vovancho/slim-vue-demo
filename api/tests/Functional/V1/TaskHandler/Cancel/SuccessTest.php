<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\TaskHandler\Cancel;

use App\Test\Functional\V1\Auth\OAuth\AuthFixture;
use App\Test\Functional\WebTestCase;

class SuccessTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            'auth' => AuthFixture::class,
            'task' => TaskFixture::class,
        ]);
    }

    public function testGuest(): void
    {
        $task = $this->getTask();
        $taskId = $task->getTask()->getId()->getValue();

        $response = $this->app()->handle(self::json('GET', "/v1/tasks/$taskId/cancel"));

        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $auth = $this->getAuth();
        $task = $this->getTask();

        $taskId = $task->getTask()->getId()->getValue();

        $response = $this->app()->handle(self::json('DELETE', "/v1/tasks/$taskId/cancel", [], $auth->getHeaders()));

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
