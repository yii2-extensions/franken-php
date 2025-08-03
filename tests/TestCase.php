<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests;

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
    protected function statelessApplication($config = []): StatelessApplication
    {
        return new StatelessApplication(
            ArrayHelper::merge(
                [
                    'id' => 'stateless-app',
                    'basePath' => __DIR__,
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
                                    'logFile' => '@runtime/log/app.log',
                                ],
                            ],
                        ],
                        'request' => [
                            'enableCookieValidation' => false,
                            'enableCsrfCookie' => false,
                            'enableCsrfValidation' => false,
                            'parsers' => [
                                'application/json' => JsonParser::class,
                            ],
                            'scriptFile' => __DIR__ . '/index.php',
                            'scriptUrl' => '/index.php',
                        ],
                        'response' => [
                            'charset' => 'UTF-8',
                        ],
                        'user' => [
                            'enableAutoLogin' => false,
                        ],
                        'urlManager' => [
                            'showScriptName' => false,
                            'enableStrictParsing' => false,
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
                    'runtimePath' => dirname(__DIR__) . '/runtime',
                    'vendorPath' => dirname(__DIR__) . '/vendor',
                ],
                $config,
            ),
        );
    }
}
