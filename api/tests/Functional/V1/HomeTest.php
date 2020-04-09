<?php

declare(strict_types=1);

namespace App\Test\Functional\V1;

use App\Test\Functional\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testMethod(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/'));

        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/'));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('{"name":"App API","version":"1.0"}', (string)$response->getBody());
    }
}
