<!-- markdownlint-disable MD041 -->
<p align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://www.yiiframework.com/image/design/logo/yii3_full_for_dark.svg">
        <source media="(prefers-color-scheme: light)" srcset="https://www.yiiframework.com/image/design/logo/yii3_full_for_light.svg">
        <img src="https://www.yiiframework.com/image/design/logo/yii3_full_for_dark.svg" alt="Yii Framework" width="80%">
    </picture>
    <h1 align="center">FrankenPHP</h1>
    <br>
</p>
<!-- markdownlint-enable MD041 -->

<p align="center">
    <a href="https://github.com/yii2-extensions/franken-php/actions/workflows/build.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/yii2-extensions/franken-php/build.yml?style=for-the-badge&logo=github&label=PHPUnit" alt="PHPUnit">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/yii2-extensions/franken-php/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=for-the-badge&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyii2-extensions%2Ffranken-php%2Fmain" alt="Mutation Testing">
    </a>
    <a href="https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/yii2-extensions/franken-php/static.yml?style=for-the-badge&logo=github&label=PHPStan" alt="PHPStan">
    </a>
</p>

<p align="center">
    <strong>Supercharge your Yii2 applications with FrankenPHP blazing-fast HTTP server</strong><br>
    <em>HTTP/2, HTTP/3, automatic memory management, and worker mode for ultimate performance</em>
</p>

## Features

<picture>
    <source media="(min-width: 768px)" srcset="./docs/svgs/features.svg">
    <img src="./docs/svgs/features-mobile.svg" alt="Feature Overview" style="width: 100%;">
</picture>

## Demo

[![Template](https://img.shields.io/badge/Template-App%20Basic-74AA9C?style=for-the-badge&logo=yii&logoColor=white)](https://github.com/yii2-extensions/app-basic/tree/franken-php)

Explore the ready-to-run Yii2 + FrankenPHP application template.

### Installation

```bash
composer require yii2-extensions/franken-php:^0.1.0@dev
```

### Quick start

Create your FrankenPHP entry point (`web/index.php`)

```php
<?php

declare(strict_types=1);

// disable PHP automatic session cookie handling
ini_set('session.use_cookies', '0');

require_once dirname(__DIR__) . '/vendor/autoload.php';

use yii2\extensions\frankenphp\FrankenPHP;
use yii2\extensions\psrbridge\http\StatelessApplication;

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// production default (change to 'true' for development)
define('YII_DEBUG', $_ENV['YII_DEBUG'] ?? false);
// production default (change to 'dev' for development)
define('YII_ENV', $_ENV['YII_ENV'] ?? 'prod');

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
    https_port 8443

    frankenphp {
        worker ./web/index.php
    }
}

localhost {
    tls ./web/ssl/localhost.pem ./web/ssl/localhost-key.pem

    log

    encode zstd br gzip

    root ./web

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

> [!WARNING]
> **Note:** Using custom certificates (like `tls ./web/ssl/localhost.pem ./web/ssl/localhost-key.pem`) avoids browser trust warnings that occur with Caddy's automatic self-signed certificates.  
> For local development, consider using [mkcert](https://github.com/FiloSottile/mkcert) to generate trusted local certificates.  
> If you want to use Caddy's automatic self-signed certificates for local development, you can remove this line.

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
./frankenphp run --config ./Caddyfile --watch
```

### Docker

Alternatively, [Docker images](https://frankenphp.dev/docs/docker/) are available.

#### Worker mode

Gitbash/Windows

<!-- editorconfig-checker-disable -->
<!-- prettier-ignore-start -->
```bash
docker run \
  -e CADDY_GLOBAL_OPTIONS="auto_https off" \
  -e CADDY_SERVER_EXTRA_DIRECTIVES="tls /app/web/ssl/localhost.pem /app/web/ssl/localhost-key.pem" \
  -e FRANKENPHP_CONFIG="worker ./web/index.php" \
  -e SERVER_NAME="https://localhost:8443" \
  -e SERVER_ROOT="./web" \
  -v "//k/yii2-extensions/app-basic:/app" \
  -p 8443:8443 \
  -p 8443:8443/udp \
  --name yii2-frankenphp-worker \
  dunglas/frankenphp
```
<!-- prettier-ignore-end -->
<!-- editorconfig-checker-enable -->

> **Note:** Paths in the example (`//k/yii2-extensions/app-basic`) are for demonstration purposes only.  
> Replace them with the actual path to your Yii2 project on your system.

Linux/WSL

<!-- editorconfig-checker-disable -->
<!-- prettier-ignore-start -->
```bash
docker run \
  -e CADDY_GLOBAL_OPTIONS="auto_https off" \
  -e CADDY_SERVER_EXTRA_DIRECTIVES="tls /app/web/ssl/localhost.pem /app/web/ssl/localhost-key.pem" \
  -e FRANKENPHP_CONFIG="worker ./web/index.php" \
  -e SERVER_NAME="https://localhost:8443" \
  -e SERVER_ROOT="./web" \
  -v $PWD:/app \
  -p 8443:8443 \
  -p 8443:8443/udp \
  --name yii2-frankenphp-worker \
  dunglas/frankenphp
```
<!-- prettier-ignore-end -->
<!-- editorconfig-checker-enable -->

> [!IMPORTANT]
> Your application will be available at `https://localhost:8443` or at the address set in the `Caddyfile`.

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

## Package information

[![PHP](https://img.shields.io/badge/%3E%3D8.1-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/releases/8.1/en.php)
[![Yii 2.0.x](https://img.shields.io/badge/2.0.53-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white)](https://github.com/yiisoft/yii2/tree/2.0.53)
[![Yii 22.0.x](https://img.shields.io/badge/22.0.x-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white)](https://github.com/yiisoft/yii2/tree/22.0)
[![Latest Stable Version](https://img.shields.io/packagist/v/yii2-extensions/franken-php.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Stable)](https://packagist.org/packages/yii2-extensions/franken-php)
[![Total Downloads](https://img.shields.io/packagist/dt/yii2-extensions/franken-php.svg?style=for-the-badge&logo=composer&logoColor=white&label=Downloads)](https://packagist.org/packages/yii2-extensions/franken-php)

## Quality code

[![Codecov](https://img.shields.io/codecov/c/github/yii2-extensions/franken-php.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Coverage)](https://codecov.io/github/yii2-extensions/franken-php)
[![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-4F5D95.svg?style=for-the-badge&logo=php&logoColor=white)](https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml)
[![Super-Linter](https://img.shields.io/github/actions/workflow/status/yii2-extensions/franken-php/linter.yml?style=for-the-badge&label=Super-Linter&logo=github)](https://github.com/yii2-extensions/franken-php/actions/workflows/linter.yml)
[![StyleCI](https://img.shields.io/badge/StyleCI-Passed-44CC11.svg?style=for-the-badge&logo=styleci&logoColor=white)](https://github.styleci.io/repos/1031393416?branch=main)

## Our social networks

[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/Terabytesoftw)

## License

[![License](https://img.shields.io/github/license/yii2-extensions/franken-php?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=333333)](LICENSE.md)
