<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require APP_PATH . 'vendor/autoload.php';

(new Dotenv(true))->load(APP_PATH . '.env');
