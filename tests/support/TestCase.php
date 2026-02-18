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
use yii\web\{IdentityInterface, JsonParser};
use yii2\extensions\psrbridge\creator\ServerRequestCreator;
use yii2\extensions\psrbridge\emitter\SapiEmitter;
use yii2\extensions\psrbridge\http\Application;

use function dirname;

/**
 * Base class for package integration tests.
 *
 * Provides a preconfigured {@see Application} instance with Yii components and PSR-7 factory bindings.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Creates an integration-test application with default components and optional overrides.
     *
     * @phpstan-param array{
     *   id?: string,
     *   basePath?: string,
     *   components?: array<string, array<string, mixed>>,
     *   container?: array{definitions?: array<string, mixed>},
     *   runtimePath?: string,
     *   vendorPath?: string
     * } $config
     * @phpstan-return Application<IdentityInterface>
     */
    protected function application(array $config = []): Application
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

        return new Application($configApplication);
    }
}
