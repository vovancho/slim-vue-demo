<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\Auth\SignUp;

use App\Auth\Service\PasswordHasher;
use Ramsey\Uuid\Uuid;
use App\Test\Functional\Json;
use App\Test\Functional\WebTestCase;

class ConfirmTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            ConfirmFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/auth/signup/confirm'));

        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup/confirm', [
            'email' => 'valid@app.test',
            'token' => ConfirmFixture::VALID,
        ]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('', (string)$response->getBody());
    }

    public function testExpired(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup/confirm', [
            'email' => 'expired@app.test',
            'token' => ConfirmFixture::EXPIRED,
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'message' => 'Код подтверждения истек.',
        ], Json::decode($body));
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup/confirm', []));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'errors' => [
                'email' => 'Значение не должно быть пустым.',
                'token' => 'Значение не должно быть пустым.',
            ],
        ], Json::decode($body));
    }

    public function testNotExisting(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup/confirm', [
            'email' => 'valid@app.test',
            'token' => ConfirmFixture::INVALID,
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'message' => 'Неверный код подтверждения.',
        ], Json::decode($body));
    }
}
