<?php

declare(strict_types=1);

namespace Api\Test\Feature\Auth\SignUp;

use Api\Test\Feature\WebTestCase;

class RequestTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->loadFixtures([
            RequestFixture::class,
        ]);

        parent::setUp();
    }

    public function testMethod(): void
    {
        $response = $this->get('/auth/signup');
        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $response = $this->post('/auth/signup', [
            'email' => 'test-mail@example.com',
            'password' => 'test-password',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'email' => 'test-mail@example.com',
        ], $data);
    }

    public function testNotValid(): void
    {
        $response = $this->post('/auth/signup', [
            'email' => 'incorrect-mail',
            'password' => 'short',
        ]);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'statusCode' => 400,
            'error' => [
                'type' => 'VALIDATION_ERROR',
                'formErrors' => [
                    'email' => 'Значение адреса электронной почты недопустимо.',
                    'password' => 'Значение слишком короткое. Должно быть равно 6 символам или больше.',
                ],
            ],
        ], $data);
    }

    public function testExisting(): void
    {
        $response = $this->post('/auth/signup', [
            'email' => 'test@example.com',
            'password' => 'test-password',
        ]);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'statusCode' => 400,
            'error' => [
                'type' => 'BAD_REQUEST',
                'description' => 'Пользователь с таким E-mail уже есть.',
            ],
        ], $data);
    }
}
