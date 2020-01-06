<?php

declare(strict_types=1);

namespace Api\Test\Feature;

class HomeTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->loadFixtures([
            'auth' => AuthFixture::class,
        ]);

        parent::setUp();
    }

    public function testSuccess(): void
    {
        $fixture = $this->getAuth();

        $response = $this->get('/', $fixture->getHeaders());

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = json_decode($content, true);

        self::assertEquals([
            'name' => 'App API',
            'version' => '1.0',
        ], $data);
    }

    private function getAuth(): AuthFixture
    {
        return $this->getFixture('auth');
    }
}
