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
app-basic/
├── config/
│   ├── console.php             # Console application configuration
│   ├── db.php                  # Database configuration
│   ├── params.php              # Application parameters
│   ├── test_db.php             # Test database configuration
│   └── web.php                 # Web application configuration
└── Caddyfile                   # Caddy server configuration
```

### Configuration files

console.php
```php
<?php

declare(strict_types=1);

use yii\caching\FileCache;
use yii\log\FileTarget;

/** @var array<string,mixed> $params */
$params = require __DIR__ . '/params.php';
/** @var array<string,mixed> $db */
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => [
                        'error',
                        'warning',
                    ],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // configuration adjustments for 'dev' environment
    // requires version `2.1.21` of yii2-debug module
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
```

db.php
```php
<?php

declare(strict_types=1);

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
```

params.php
```php
<?php

declare(strict_types=1);

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
];
```

test_db.php
```php
<?php

declare(strict_types=1);

/** @var array<string,mixed> $db */
$db = require __DIR__ . '/db.php';

// test database! Important not to run tests on production or development databases
$db['dsn'] = 'mysql:host=localhost;dbname=yii2basic_test';

return $db;
```

test.php
```php
<?php

declare(strict_types=1);

use app\models\User;
use yii\symfonymailer\{Mailer, Message};

/** @var array<string,mixed> $params */
$params = require __DIR__ . '/params.php';
/** @var array<string,mixed> $db */
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [       
        'db' => $db,
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
            'messageClass' => Message::class,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => User::class,
        ],
        'request' => [
            // note: this key is for testing only. Replace with a secure, random string in production!
            'cookieValidationKey' => 'your-cookie-validation-key',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => $params,
];
```

web.php
```php
<?php

declare(strict_types=1);

use app\models\User;
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
use yii\caching\FileCache;
use yii\di\Instance;
use yii\log\FileTarget;
use yii\gii\Module;
use yii\symfonymailer\Mailer;
use yii2\extensions\debug\WorkerDebugModule;

/** @var array<string,mixed> $params */
$params = require __DIR__ . '/params.php';
/** @var array<string,mixed> $db */
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [  
        'request' => [
            // note: this key is for testing only. Replace with a secure, random string in production!
            'cookieValidationKey' => 'your-cookie-validation-key',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'user' => [
            'enableAutoLogin' => false, // disable auto-login for stateless application
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => [
                        'error',
                        'warning',
                    ],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'container' => [
        'definitions' => [
            ResponseFactoryInterface::class => ResponseFactory::class,
            ServerRequestFactoryInterface::class => ServerRequestFactory::class,
            StreamFactoryInterface::class => StreamFactory::class,
            UploadedFileFactoryInterface::class => UploadedFileFactory::class
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => WorkerDebugModule::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
```

## Next steps

- 🧪 [Testing Guide](testing.md)
