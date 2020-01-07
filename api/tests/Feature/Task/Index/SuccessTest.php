<?php

declare(strict_types=1);

namespace Api\Test\Feature\Task\Index;


use Api\Test\Feature\AuthFixture;
use Api\Test\Feature\WebTestCase;

class SuccessTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->loadFixtures([
            'auth' => AuthFixture::class,
            'tasks' => TasksFixture::class,
        ]);

        parent::setUp();
    }

    public function testSuccess(): void
    {
        $auth = $this->getAuth();
        $tasks = $this->getTasks();

        $response = $this->get('/tasks', $auth->getHeaders());

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals($tasks->tasksCount(), $data['total']);
        self::assertCount($tasks->tasksCount(), $data['rows']);

        $firstTask = array_shift($data['rows']);

        self::assertArrayHasKey('id', $firstTask);
        self::assertArrayHasKey('pushed_at', $firstTask);
        self::assertArrayHasKey('user_id', $firstTask);
        self::assertArrayHasKey('user_email', $firstTask);
        self::assertArrayHasKey('type', $firstTask);
        self::assertArrayHasKey('name', $firstTask);
        self::assertArrayHasKey('status', $firstTask);
        self::assertArrayHasKey('process_percent', $firstTask);
        self::assertArrayHasKey('error_message', $firstTask);
        self::assertArrayHasKey('position', $firstTask);

        self::assertEquals('Task3', $firstTask['name']);
        self::assertEquals(3, $firstTask['position']);
    }

    public function testPagination()
    {
        $auth = $this->getAuth();
        $this->getTasks();

        $response = $this->get('/tasks?page=3&itemsPerPage=1', $auth->getHeaders());

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals(3, $data['total']);
        self::assertCount(1, $data['rows']);

        $firstTask = array_shift($data['rows']);

        self::assertEquals('Task1', $firstTask['name']);
        self::assertEquals(1, $firstTask['position']);
    }

    public function testSort()
    {
        $auth = $this->getAuth();
        $this->getTasks();

        $response = $this->get('/tasks?page=1&itemsPerPage=1&sortBy[]=name&sortDesc[]=false', $auth->getHeaders());
        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals(3, $data['total']);
        self::assertCount(1, $data['rows']);

        $firstTask = array_shift($data['rows']);

        self::assertEquals('Task1', $firstTask['name']);
        self::assertEquals(1, $firstTask['position']);
    }

    private function getAuth(): AuthFixture
    {
        return $this->getFixture('auth');
    }

    private function getTasks(): TasksFixture
    {
        return $this->getFixture('tasks');
    }
}
