# Configuration reference

## Overview

This guide covers all configuration options for the FrankenPHP extension, from basic setup to advanced production
scenarios.

## Basic configuration

### Constructor parameters

`FrankenPHP` class accepts a `StatelessApplication` instance.

```php
<?php

declare(strict_types=1);

use yii2\extensions\psrbridge\http\StatelessApplication;
use yii2\extensions\frankenphp\FrankenPHP;

$app = new StatelessApplication($config);
$runner = new FrankenPHP($app);
```

#### Memory management

Configure automatic worker restart based on memory usage.

```php
$app = new StatelessApplication($config);

// set memory limit in bytes (default: 128MB)
$app->setMemoryLimit(256 * 1024 * 1024); // 256MB

$runner = new FrankenPHP($app);
```

#### Clean state detection

Application automatically checks if it's in a clean state after each request.

```php

if ($app->clean()) {
    // worker will be restarted
}
```

## Stateless application settings

### Configuration structure

Organize your project for FrankenPHP:

```text
your-project/
├── config/
│   └── common/                 # Common configuration  
│       ├── components.php      # Components
│   └── web/                    # Web configuration
│       ├── app.php             # Application
│       ├── components.php      # Components
│       ├── container.php       # Container
│       └── modules.php         # Modules
├── .env                        # Environment variables
├── Caddyfile                   # FrankenPHP configuration
├── frankenphp.yaml             # Alternative YAML config (optional)
└── frankenphp                  # FrankenPHP binary
```

### Configuration files

#### Common

components.php
```php
<?php

declare(strict_types=1);

use yii\caching\ArrayCache;
use yii\log\FileTarget;
use yii\symfonymailer\Mailer;

return [
    'cache' => [
        'class' => ArrayCache::class,
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
                'logFile' => '@runtime/logs/app.log',
            ],
        ],
    ],
    'mailer' => [
        'class' => Mailer::class,
        'useFileTransport' => true,
    ],
];
```

#### Web

app.php
```php
<?php

declare(strict_types=1);

use app\framework\event\ContactEventHandler;
use app\usecase\contact\ContactController;
use app\usecase\security\SecurityController;
use app\usecase\site\SiteController;

/** @phpstan-var array<string,mixed> $components */
$components = require __DIR__ . '/components.php';
/** @phpstan-var array<string,mixed> $container */
$container = require __DIR__ . '/container.php';
/** @phpstan-var array<string,mixed> $modules */
$modules = require __DIR__ . '/modules.php';
/** @phpstan-var array<string,mixed> $params */
$params = require dirname(__DIR__) . '/params-web.php';

$rootDir = dirname(__DIR__, 2);

$config = [
    'id' => 'web.basic',
    'aliases' => [
        '@root' => $rootDir,
        '@npm' => '@root/node_modules',
        '@bower' => '@npm',
        '@public' => '@root/public',
        '@resource' => '@root/src/framework/resource',
        '@runtime' => '@root/runtime',
        '@web' => '/',
    ],
    'basePath' => $rootDir,
    'bootstrap' => [
        ContactEventHandler::class,
        'log',
    ],
    'components' => $components,
    'container' => $container,
    'controllerMap' => [
        'contact' => [
            'class' => ContactController::class,
        ],
        'security' => [
            'class' => SecurityController::class,
        ],
        'site' => [
            'class' => SiteController::class,
        ],
    ],
    'language' => 'en-US',
    'modules' => $modules,
    'name' => 'Web application basic',
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
```

components.php
```php
<?php

declare(strict_types=1);

use app\usecase\security\Identity;
use yii2\extensions\localeurls\UrlLanguageManager;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;
use yii\web\User;

/** @phpstan-var array<string,mixed> $commonComponents */
$commonComponents = require dirname(__DIR__) . '/common/components.php';

$config = [
    'assetManager' => [
        'basePath' => '@public/assets',
    ],
    'errorHandler' => [
        'errorAction' => 'site/404',
    ],
    'i18n' => [
        'translations' => [
            'app.basic' => [
                'class' => PhpMessageSource::class,
                'basePath' => '@resource/message',
                'sourceLanguage' => 'en',
            ],
        ],
    ],
    'urlManager' => [
        'class' => UrlLanguageManager::class,
        'languages' => [
            'de' => 'de-DE',
            'en' => 'en-US',
            'es' => 'es-ES',
            'fr' => 'fr-FR',
            'pt' => 'pt-BR',
            'ru' => 'ru-RU',
            'zh' => 'zh-CN',
        ],
        'enableDefaultLanguageUrlCode' => false,
        'enableLanguageDetection' => false,
        'enableLanguagePersistence' => false,
        'enablePrettyUrl' => true,
        'keepUppercaseLanguageCode' => false,
        'languageCookieDuration' => 1800,
        'languageSessionKey' => false,
        'showScriptName' => false,
    ],
    'user' => [
        'class' => User::class,
        'identityClass' => Identity::class,
    ],
];

return ArrayHelper::merge($commonComponents, $config);
```

container.php
```php
<?php

declare(strict_types=1);

use HttpSoft\Message\{
    ResponseFactory,
    ServerRequestFactory,
    StreamFactory,
    UploadedFileFactory,
};
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
};

return [
    'definitions' => [
        ResponseFactoryInterface::class => ResponseFactory::class,
        ServerRequestFactoryInterface::class => ServerRequestFactory::class,
        StreamFactoryInterface::class => StreamFactory::class,
        UploadedFileFactoryInterface::class => UploadedFileFactory::class,        
    ],
];
```

modules.php
```php
<?php

declare(strict_types=1);

return []; // Add your modules here
```

## Next steps

- 🧪 [Testing Guide](testing.md)
