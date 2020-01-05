<?php

declare(strict_types=1);

namespace Api\Test\Feature\Auth\SignUp;

use Api\Test\Feature\WebTestCase;

class ConfirmTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->loadFixtures([
            ConfirmFixture::class,
        ]);

        parent::setUp();
    }

    public function testMethod(): void
    {
        $this->expectExceptionMessage('Method not allowed. Must be one of: POST');
        $response = $this->get('/auth/signup');
        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $response = $this->post('/auth/signup/confirm', [
            'email' => 'confirm@example.com',
            'token' => 'token',
        ]);

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([], $data);
    }

    public function testNotValid(): void
    {
        $response = $this->post('/auth/signup/confirm', [
            'email' => 'not-valid',
            'token' => '',
        ]);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'errors' => [
                'email' => 'Значение адреса электронной почты недопустимо.',
                'token' => 'Значение не должно быть пустым.',

            ],
        ], $data);
    }

    public function testNotExistingUser(): void
    {
        $response = $this->post('/auth/signup/confirm', [
            'email' => 'not-found@example.com',
            'token' => 'token',
        ]);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'error' => 'Пользователь не найден.',
        ], $data);
    }

    public function testInvalidToken(): void
    {
        $response = $this->post('/auth/signup/confirm', [
            'email' => 'confirm@example.com',
            'token' => 'incorrect',
        ]);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'error' => 'Неверный код подтверждения.',
        ], $data);
    }

    public function testExpiredToken(): void
    {
        $response = $this->post('/auth/signup/confirm', [
            'email' => 'expired@example.com',
            'token' => 'token',
        ]);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'error' => 'Код подтверждения истек.',
        ], $data);
    }
}
