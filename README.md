<p align="center">
    <a href="https://github.com/yii2-extensions/franken-php" target="_blank">
        <img src="https://www.yiiframework.com/image/yii_logo_light.svg" alt="Yii Framework">
    </a>
    <h1 align="center">Extension for FrankenPHP</h1>
    <br>
</p>

<p align="center">
    <a href="https://www.php.net/releases/8.2/en.php" target="_blank">
        <img src="https://img.shields.io/badge/PHP-%3E%3D8.2-787CB5" alt="PHP Version">
    </a>
    <a href="https://github.com/yiisoft/yii2/tree/2.0.53" target="_blank">
        <img src="https://img.shields.io/badge/Yii2%20-2.0.53-blue" alt="Yii2 2.0.53">
    </a>
    <a href="https://github.com/yiisoft/yii2/tree/22.0" target="_blank">
        <img src="https://img.shields.io/badge/Yii2%20-22-blue" alt="Yii2 22.0">
    </a>
    <a href="https://github.com/yii2-extensions/franken-php/actions/workflows/build.yml" target="_blank">
        <img src="https://github.com/yii2-extensions/franken-php/actions/workflows/build.yml/badge.svg" alt="PHPUnit">
    </a> 
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/yii2-extensions/franken-php/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyii2-extensions%2Ffranken-php%2Fmain" alt="Mutation Testing">
    </a>        
    <a href="https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml" target="_blank">        
        <img src="https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml/badge.svg" alt="Static Analysis">
    </a>  
</p>

A blazing-fast FrankenPHP integration for Yii2 applications that provides seamless HTTP/2 and HTTP/3 support, automatic
memory management, and real-time capabilities.

## Features

- ✅ **Automatic Memory Management**: Smart cleanup with configurable memory limits.
- ✅ **Error Handling**: Comprehensive error reporting to FrankenPHP worker.
- ✅ **Graceful Shutdown**: Automatic worker restart when memory usage is high.
- ✅ **High Performance**: Utilize FrankenPHP blazing-fast HTTP server for your Yii2 applications.
- ✅ **HTTP/2 & HTTP/3 Support**: Native support for modern HTTP protocols with multiplexing.
- ✅ **Production Ready**: Battle-tested with Caddy proven reliability.
- ✅ **PSR-7 Compatible**: Full PSR-7 request/response handling through the PSR bridge.
- ✅ **Stateless Design**: Memory-efficient stateless application lifecycle.
- ✅ **Zero Configuration**: Works out of the box with minimal setup.

## Quick start

### Installation

```bash
composer require yii2-extensions/franken-php:^0.1.0@dev
```

### Basic Usage

Create your FrankenPHP entry point (`public/index.php`)
```php
<?php

declare(strict_types=1);

use yii2\extensions\frankenphp\FrankenPHP;
use yii2\extensions\psrbridge\http\StatelessApplication;

// production default (change to 'true' for development)
defined('YII_DEBUG') or define('YII_DEBUG', false);

// production default (change to 'dev' for development)
defined('YII_ENV') or define('YII_ENV', 'prod');

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

$config = require_once dirname(__DIR__) . '/config/web.php';

$runner = new FrankenPHP(new StatelessApplication($config));

$runner->run();
```

### FrankenPHP configuration

Create `Caddyfile` in your project root (worker mode)
```caddyfile
{
    auto_https off
    admin localhost:2019

    frankenphp {
        worker ./index.php
    }
}

localhost {
    log

    encode zstd br gzip

    root public/

    request_header X-Sendfile-Type x-accel-redirect
    request_header X-Accel-Mapping ../private-files=/private-files

    intercept {
        @sendfile header X-Accel-Redirect *
        handle_response @sendfile {
            root private-files/
            rewrite * {resp.header.X-Accel-Redirect}
            method * GET
            header -X-Accel-Redirect
            file_server
        }
    }

    php_server {
        try_files {path} index.php
    }
}
```

### Standalone Binary

We provide static FrankenPHP binaries for Linux and macOS containing [PHP 8.4](https://www.php.net/releases/8.4/en.php) 
and most popular PHP extensions.

On Windows, use [WSL](https://learn.microsoft.com/windows/wsl/) to run FrankenPHP.

[Download FrankenPHP](https://github.com/php/frankenphp/releases) or copy this line into your terminal to automatically
install the version appropriate for your platform.

```bash
curl https://frankenphp.dev/install.sh | sh
mv frankenphp /usr/local/bin/
```

To run your application, you can use the following command.

```bash
cd web
./frankenphp php-server --worker index.php
```

### Docker

Docker image require `public` directory to be mounted as `/app/public` and the application root directory as `/app`.

Alternatively, [Docker images](https://frankenphp.dev/docs/docker/) are available.

#### Worker mode

Gitbash/Windows
```bash
docker run \
  -e FRANKENPHP_CONFIG="worker ./public/index.php" \
  -v "//k/yii2-extensions/basic-frankenphp/Caddyfile:/etc/caddy/Caddyfile" \
  -v "//k/yii2-extensions/basic-frankenphp:/app" \
  -v "//k/yii2-extensions/basic-frankenphp/web:/app/public" \
  -p 80:80 \
  -p 443:443 \
  -p 443:443/udp \
  --name yii2-frankenphp-worker \
  dunglas/frankenphp:php8.3-alpine
```
> **Note:** Paths in the example (`//k/yii2-extensions/basic-frankenphp`) are for demonstration purposes only.  
> Replace them with the actual path to your Yii2 project on your system.

Linux/WSL
```bash
docker run \
  -e FRANKENPHP_CONFIG="worker ./public/index.php" \
  -v $PWD/Caddyfile:/etc/caddy/Caddyfile \
  -v $PWD:/app \
  -v $PWD/web:/app/public \
  -p 80:80 \
  -p 443:443 \
  -p 443:443/udp \
  --name yii2-frankenphp-worker \
  dunglas/frankenphp:php8.3-alpine
```

### Development & Debugging

For enhanced debugging capabilities and proper time display in FrankenPHP, install the worker debug extension.

```bash
composer require --dev yii2-extensions/worker-debug:^0.1
```

Add the following to your development configuration (`config/web.php`):

```php
<?php

declare(strict_types=1);

use yii2\extensions\debug\WorkerDebugModule;

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => WorkerDebugModule::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}
```

## Documentation

For detailed configuration options and advanced usage.
- 📚 [Installation Guide](docs/installation.md)
- ⚙️ [Configuration Reference](docs/configuration.md) 
- 🧪 [Testing Guide](docs/testing.md)

## Quality code

[![Latest Stable Version](https://poser.pugx.org/yii2-extensions/franken-php/v)](https://github.com/yii2-extensions/franken-php/releases)
[![Total Downloads](https://poser.pugx.org/yii2-extensions/franken-php/downloads)](https://packagist.org/packages/yii2-extensions/franken-php)
[![codecov](https://codecov.io/gh/yii2-extensions/franken-php/graph/badge.svg?token=Upc4yA23YN)](https://codecov.io/gh/yii2-extensions/franken-php)
[![phpstan-level](https://img.shields.io/badge/PHPStan%20level-max-blue)](https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml)
[![StyleCI](https://github.styleci.io/repos/1031393416/shield?branch=main)](https://github.styleci.io/repos/1031393416?branch=main)

## Our social networks

[![X](https://img.shields.io/badge/follow-@terabytesoftw-1DA1F2?logo=x&logoColor=1DA1F2&labelColor=555555&style=flat)](https://x.com/Terabytesoftw)

## License

[![License](https://img.shields.io/github/license/yii2-extensions/franken-php?cacheSeconds=0)](LICENSE.md)
