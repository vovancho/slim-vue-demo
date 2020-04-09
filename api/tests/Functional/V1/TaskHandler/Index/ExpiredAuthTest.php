<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\TaskHandler\Index;

use App\Test\Functional\Json;
use App\Test\Functional\V1\Auth\OAuth\AuthExpiredFixture;
use App\Test\Functional\WebTestCase;

class ExpiredAuthTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            'auth' => AuthExpiredFixture::class,
            'tasks' => TasksFixture::class,
        ]);
    }

    public function testExpired(): void
    {
        $auth = $this->getAuth();

        $response = $this->app()->handle(self::json('GET', '/v1/tasks', [], $auth->getHeaders()));

        self::assertEquals(401, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'message' => 'The resource owner or authorization server denied the request.',
        ], Json::decode($body));
    }

    private function getAuth(): AuthExpiredFixture
    {
        return $this->getFixture('auth');
    }
}
