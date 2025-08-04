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

A blazing-fast FrankenPHP integration for Yii2 applications that provides seamless HTTP/2, HTTP/3, and WebSocket support 
with automatic memory management and real-time capabilities.

## Features

- ✅ **Automatic HTTPS**: Built-in automatic HTTPS with Let's Encrypt integration.
- ✅ **Early Hints**: HTTP/2 Server Push and Early Hints support for faster loading.
- ✅ **Graceful Reloading**: Hot reload capabilities without downtime.
- ✅ **HTTP/2 & HTTP/3 Support**: Native support for modern HTTP protocols with multiplexing.
- ✅ **Memory Efficient**: Smart memory management with configurable limits.
- ✅ **Production Ready**: Battle-tested with Caddy's proven reliability.
- ✅ **Real-time Features**: Server-Sent Events (SSE) and WebSocket support.
- ✅ **WebSocket Integration**: Real-time bidirectional communication capabilities.
- ✅ **Worker Mode**: Long-running worker processes for maximum performance.
- ✅ **Zero Configuration**: Works out of the box with sensible defaults.

## Quick start

### Installation

```bash
composer require yii2-extensions/franken-php
```

### Basic Usage

Create your FrankenPHP entry point (`public/index.php`):

```php
<?php

declare(strict_types=1);

use yii2\extensions\psrbridge\http\StatelessApplication;
use yii2\extensions\franken-php\FrankenPHP;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require dirname(__DIR__) . '/config/web/app.php';

$runner = new FrankenPHP(new StatelessApplication($config));

$runner->run();
```

### Standalone Binary

We provide static FrankenPHP binaries for Linux and macOS containing [PHP 8.4](https://www.php.net/releases/8.4/en.php) 
and most popular PHP extensions.

On Windows, use [WSL](https://learn.microsoft.com/windows/wsl/) to run FrankenPHP.

[Download FrankenPHP](https://github.com/php/frankenphp/releases) or copy this line into your terminal to automatically
install the version appropriate for your platform.

```console
curl https://frankenphp.dev/install.sh | sh
mv frankenphp /usr/local/bin/
```

To serve the content of the current directory, run.
```console
frankenphp php-server
```

You can also run command-line scripts with.
```console
frankenphp php-cli /path/to/your/script.php
```

### Docker

Alternatively, [Docker images](https://frankenphp.dev/docs/docker/) are available:

```console
docker run -v .:/app/public \
    -p 80:80 -p 443:443 -p 443:443/udp \
    dunglas/frankenphp
```

### Start the server

```bash
# start the server
./franken-php run

# or with worker mode for production
./franken-php run --worker public/index.php
```

Your application will be available at `https://localhost` (HTTPS by default) and you can access it via HTTP/2 or HTTP/3.

## Documentation

For detailed configuration options and advanced usage:

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
