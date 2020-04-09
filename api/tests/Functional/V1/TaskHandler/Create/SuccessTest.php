<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\TaskHandler\Create;

use App\TaskHandler\Entity\Task\Visibility;
use App\Test\Functional\Json;
use App\Test\Functional\V1\Auth\OAuth\AuthFixture;
use App\Test\Functional\WebTestCase;

class SuccessTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            'auth' => AuthFixture::class,
        ]);
    }

    public function testGuest(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/tasks/create'));

        self::assertEquals(405, $response->getStatusCode());
    }

    /**
     * @dataProvider taskVisibilitiesProvider
     * @param string $taskVisibility
     */
    public function testSuccess(string $taskVisibility): void
    {
        $auth = $this->getAuth();

        $response = $this->app()->handle(self::json('POST', '/v1/tasks/create', [
            'visibility' => $visibility = $taskVisibility,
            'name' => $name = 'Name',
        ], $auth->getHeaders()));

        self::assertEquals(201, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertArrayHasKey('id', $data);
        self::assertNotEmpty($data['id']);
        self::assertArrayHasKey('pushed_at', $data);
        self::assertNotEmpty($data['pushed_at']);
    }

    public function taskVisibilitiesProvider()
    {
        return [
            [Visibility::PRIVATE],
            [Visibility::PUBLIC],
        ];
    }

    private function getAuth(): AuthFixture
    {
        return $this->getFixture('auth');
    }
}
