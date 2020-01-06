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
        $this->expectExceptionMessage('Method not allowed. Must be one of: POST');
        $response = $this->get('/task/create');
        self::assertEquals(401, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $auth = $this->getAuth();

        $response = $this->post('/task/create', [
            'type' => $type = Task::TYPE_PRIVATE,
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

    private function getAuth(): AuthFixture
    {
        return $this->getFixture('auth');
    }
}
