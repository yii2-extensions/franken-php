<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests\support;

use HttpSoft\Message\{ResponseFactory, ServerRequestFactory, StreamFactory, UploadedFileFactory};
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
};
use yii\caching\FileCache;
use yii\helpers\ArrayHelper;
use yii\log\FileTarget;
use yii\web\JsonParser;
use yii2\extensions\psrbridge\creator\ServerRequestCreator;
use yii2\extensions\psrbridge\emitter\SapiEmitter;
use yii2\extensions\psrbridge\http\StatelessApplication;

use function dirname;

/**
 * Base test case providing common helpers and utilities for FrankenPHP extension tests.
 *
 * Provides utilities to create Yii2 stateless application instances configured for FrankenPHP testing environments.
 *
 * The test case sets up a pre-configured application with PSR-7 factories, caching, logging, and routing capabilities
 * suitable for testing FrankenPHP integration with Yii2.
 *
 * Tests that require HTTP `request`/`response` factories, stream factories, or stateless application scaffolding should
 * extend this class.
 *
 * Key features.
 * - Configures URL routing with pretty URLs and custom routing patterns.
 * - Creates `StatelessApplication` instances with a sane test configuration for FrankenPHP.
 * - Pre-configures PSR-7 factories (ResponseFactory, ServerRequestFactory, StreamFactory, UploadedFileFactory).
 * - Provides file caching and logging components for test scenarios.
 * - Sets up PSR bridge components (ServerRequestCreator, SapiEmitter) in the dependency injection container.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @phpstan-param array{
     *   id?: string,
     *   basePath?: string,
     *   components?: array<string, array<string, mixed>>,
     *   container?: array{definitions?: array<string, mixed>},
     *   runtimePath?: string,
     *   vendorPath?: string
     * } $config
     */
    protected function statelessApplication(array $config = []): StatelessApplication
    {
        /** @phpstan-var array<string, mixed> $configApplication */
        $configApplication = ArrayHelper::merge(
            [
                'id' => 'stateless-app',
                'basePath' => dirname(__DIR__, 2),
                'bootstrap' => ['log'],
                'components' => [
                    'cache' => [
                        'class' => FileCache::class,
                    ],
                    'log' => [
                        'traceLevel' => YII_DEBUG ? 3 : 0,
                        'targets' => [
                            [
                                'class' => FileTarget::class,
                                'levels' => [
                                    'error',
                                    'info',
                                    'warning',
                                ],
                            ],
                        ],
                    ],
                    'request' => [
                        'cookieValidationKey' => 'test-franken-php',
                        'parsers' => [
                            'application/json' => JsonParser::class,
                        ],
                        'scriptFile' => __DIR__ . '/index.php',
                        'scriptUrl' => '/index.php',
                    ],
                    'user' => [
                        'enableAutoLogin' => false,
                    ],
                    'urlManager' => [
                        'showScriptName' => false,
                        'enablePrettyUrl' => true,
                        'rules' => [
                            [
                                'pattern' => '/<controller>/<action>/<test:\w+>',
                                'route' => '<controller>/<action>',
                            ],
                        ],
                    ],
                ],
                'container' => [
                    'definitions' => [
                        ResponseFactoryInterface::class => ResponseFactory::class,
                        ServerRequestFactoryInterface::class => ServerRequestFactory::class,
                        StreamFactoryInterface::class => StreamFactory::class,
                        UploadedFileFactoryInterface::class => UploadedFileFactory::class,
                        ServerRequestCreator::class => ServerRequestCreator::class,
                        SapiEmitter::class => SapiEmitter::class,
                    ],
                ],
            ],
            $config,
        );

        return new StatelessApplication($configApplication);
    }
}
