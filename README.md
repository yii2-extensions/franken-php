<p align="center">
    <a href="https://github.com/yii2-extensions/franken-php" target="_blank">
        <img src="https://www.yiiframework.com/image/yii_logo_light.svg" alt="Yii Framework">
    </a>
    <h1 align="center">Extension for FrankenPHP</h1>
    <br>
</p>

<p align="center">
    <a href="https://www.php.net/releases/8.1/en.php" target="_blank">
        <img src="https://img.shields.io/badge/%3E%3D8.1-777BB4.svg?style=for-the-badge&logo=php&logoColor=white" alt="PHP version">
    </a>
    <a href="https://github.com/yiisoft/yii2/tree/2.0.53" target="_blank">
        <img src="https://img.shields.io/badge/2.0.x-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white" alt="Yii 2.0.x">
    </a>
    <a href="https://github.com/yiisoft/yii2/tree/22.0" target="_blank">
        <img src="https://img.shields.io/badge/22.0.x-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white" alt="Yii 22.0.x">
    </a>
    <a href="https://github.com/yii2-extensions/franken-php/actions/workflows/build.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/yii2-extensions/franken-php/build.yml?style=for-the-badge&label=PHPUnit" alt="PHPUnit">
    </a>
    <a href="https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/yii2-extensions/franken-php/static.yml?style=for-the-badge&label=PHPStan" alt="PHPStan">
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

### Installation

[![Latest Stable Version](https://img.shields.io/packagist/v/yii2-extensions/franken-php.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Stable)](https://packagist.org/packages/yii2-extensions/franken-php)
[![Total Downloads](https://img.shields.io/packagist/dt/yii2-extensions/franken-php.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Downloads)](https://packagist.org/packages/yii2-extensions/franken-php)

## Quick start

```bash
composer require yii2-extensions/franken-php:^0.1.0@dev
```

### Basic Usage

Create your FrankenPHP entry point (`web/index.php`)
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

// disable PHP automatic session cookie handling
ini_set('session.use_cookies', '0');

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

    root {$SERVER_ROOT:public/}

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

Docker images require the `web` directory to be mounted as `/app/web` and the application root directory as `/app`.

Alternatively, [Docker images](https://frankenphp.dev/docs/docker/) are available.

#### Worker mode

Gitbash/Windows
```bash
docker run \
  -e FRANKENPHP_CONFIG="worker ./web/index.php" \
  -e SERVER_ROOT=./web \
  -v "//k/yii2-extensions/basic-frankenphp/Caddyfile:/etc/caddy/Caddyfile" \
  -v "//k/yii2-extensions/basic-frankenphp:/app" \
  -v "//k/yii2-extensions/basic-frankenphp/web:/app/web" \
  -p 80:80 \
  -p 443:443 \
  -p 443:443/udp \
  --name yii2-frankenphp-worker \
  dunglas/frankenphp
```
> **Note:** Paths in the example (`//k/yii2-extensions/basic-frankenphp`) are for demonstration purposes only.  
> Replace them with the actual path to your Yii2 project on your system.

Linux/WSL
```bash
docker run \
  -e FRANKENPHP_CONFIG="worker ./web/index.php" \
  -e SERVER_ROOT=./web \  
  -v $PWD/Caddyfile:/etc/caddy/Caddyfile \
  -v $PWD:/app \
  -v $PWD/web:/app/web \
  -p 80:80 \
  -p 443:443 \
  -p 443:443/udp \
  --name yii2-frankenphp-worker \
  dunglas/frankenphp
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

### File Upload Handling

For enhanced file upload support in worker environments, use the PSR-7 bridge UploadedFile class instead of the standard 
Yii2 implementation.

```php
<?php

declare(strict_types=1);

use yii2\extensions\psrbridge\http\{Response, UploadedFile};

final class FileController extends \yii\web\Controller
{
    public function actionUpload(): Response
    {
        $file = UploadedFile::getInstanceByName('avatar');
        
        if ($file !== null && $file->error === UPLOAD_ERR_OK) {
            $file->saveAs('@webroot/uploads/' . $file->name);
        }
        
        return $this->asJson(['status' => 'uploaded']);
    }
}
```

## Documentation

For detailed configuration options and advanced usage.
- 📚 [Installation Guide](docs/installation.md)
- ⚙️ [Configuration Reference](docs/configuration.md) 
- 🧪 [Testing Guide](docs/testing.md)

## Quality code

[![Codecov](https://img.shields.io/codecov/c/github/yii2-extensions/franken-php.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Coverage)](https://codecov.io/github/yii2-extensions/franken-php)
[![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-4F5D95.svg?style=for-the-badge&logo=php&logoColor=white)](https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml)
[![StyleCI](https://img.shields.io/badge/StyleCI-Passed-44CC11.svg?style=for-the-badge&logo=styleci&logoColor=white)](https://github.styleci.io/repos/1031393416?branch=main)

## Our social networks

[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/Terabytesoftw)

## License

[![License](https://img.shields.io/github/license/yii2-extensions/franken-php?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=333333)](LICENSE.md)
