<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ArrowFunction\ArrowFunctionDelegatingCallToFirstClassCallableRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/vendor/php-forge/coding-standard/src/rector-83.php');

    $rectorConfig->importNames(true, false);

    $rectorConfig->skip(
        [
            ArrowFunctionDelegatingCallToFirstClassCallableRector::class => [
                __DIR__ . '/tests/support/MockerExtension.php',
            ],
        ],
    );

    $rectorConfig->paths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ],
    );
};
