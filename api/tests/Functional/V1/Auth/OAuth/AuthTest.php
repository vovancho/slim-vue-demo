<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\Auth\OAuth;

use App\Test\Functional\Json;
use App\Test\Functional\WebTestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

class AuthTest extends WebTestCase
{
    use ArraySubsetAsserts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            AuthFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/oauth/auth'));

        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/oauth/auth', [
            'grant_type' => 'password',
            'username' => 'oauth@app.test',
            'password' => 'password-hash',
            'client_id' => 'app',
            'client_secret' => '',
            'access_type' => 'offline',
        ]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertArraySubset([
            'token_type' => 'Bearer',
        ], $data);

        self::assertArrayHasKey('expires_in', $data);
        self::assertNotEmpty($data['expires_in']);

        self::assertArrayHasKey('access_token', $data);
        self::assertNotEmpty($data['access_token']);

        self::assertArrayHasKey('refresh_token', $data);
        self::assertNotEmpty($data['refresh_token']);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/oauth/auth', [
            'grant_type' => 'password',
            'username' => 'oauth@app.test',
            'password' => 'invalid',
            'client_id' => 'app',
            'client_secret' => '',
            'access_type' => 'offline',
        ]));

        self::assertEquals(400, $response->getStatusCode());
    }
}
