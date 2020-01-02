<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 29.12.2019
 * Time: 9:55
 */

declare(strict_types=1);

use DI\ContainerBuilder;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

return function (ContainerBuilder $containerBuilder) {
    $aggregator = new ConfigAggregator([
        new PhpFileProvider(__DIR__ . '/common/*.php'),
        new PhpFileProvider(__DIR__ . '/' . (getenv('API_ENV') ?: 'prod') . '/*.php'),
    ]);

    $config = $aggregator->getMergedConfig();
    $containerBuilder->addDefinitions($config);
};
