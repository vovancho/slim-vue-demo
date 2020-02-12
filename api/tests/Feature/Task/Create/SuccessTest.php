<?php

declare(strict_types=1);

namespace Api\Test\Feature\Task\Create;

use Api\Model\Task\Entity\Task\Task;
use Api\Test\Feature\AuthFixture;
use Api\Test\Feature\WebTestCase;

class SuccessTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->loadFixtures([
            'auth' => AuthFixture::class,
        ]);

        parent::setUp();
    }

    public function testGuest(): void
    {
        $response = $this->get('/tasks/create');
        self::assertEquals(405, $response->getStatusCode());
    }

    /**
     * @dataProvider taskTypesProvider
     * @param $taskType
     */
    public function testSuccess($taskType): void
    {
        $auth = $this->getAuth();

        $response = $this->post('/tasks/create', [
            'type' => $type = $taskType,
            'name' => $name = 'Name',
        ], $auth->getHeaders());

        self::assertEquals(201, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertArrayHasKey('id', $data);
        self::assertNotEmpty($data['id']);
        self::assertArrayHasKey('pushed_at', $data);
        self::assertNotEmpty($data['pushed_at']);
    }

    public function taskTypesProvider()
    {
        return [
            [Task::TYPE_PRIVATE],
            [Task::TYPE_PUBLIC],
        ];
    }

    private function getAuth(): AuthFixture
    {
        return $this->getFixture('auth');
    }
}
