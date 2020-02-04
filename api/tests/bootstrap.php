<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require APP_PATH . 'vendor/autoload.php';

if (file_exists('.env.test')) {
    (new Dotenv(true))->load('.env.test');
}
