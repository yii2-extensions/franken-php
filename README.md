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
composer require yii2-extensions/franken-php:^0.4@dev httpsoft/http-message
```

### Quick start

Create `web/index.php` in an existing Yii application with its routes and secret `request.cookieValidationKey`
already configured.

```php
<?php

declare(strict_types=1);

use HttpSoft\Message\{ResponseFactory, ServerRequestFactory, StreamFactory, UploadedFileFactory};
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
};
use yii2\extensions\frankenphp\FrankenPHP;
use yii2\extensions\psrbridge\http\Application;

ini_set('session.use_cookies', '0');

define('YII_DEBUG', filter_var(getenv('YII_DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('YII_ENV', getenv('YII_ENV') ?: 'prod');

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

$config = require dirname(__DIR__) . '/config/web.php';

$config['container']['definitions'][ResponseFactoryInterface::class] = ResponseFactory::class;
$config['container']['definitions'][ServerRequestFactoryInterface::class] = ServerRequestFactory::class;
$config['container']['definitions'][StreamFactoryInterface::class] = StreamFactory::class;
$config['container']['definitions'][UploadedFileFactoryInterface::class] = UploadedFileFactory::class;

$runner = new FrankenPHP(new Application($config));
$runner->run();
```

The runner emits each PSR response and then completes the bridge's `finalize()` lifecycle exactly once. HEAD omits
the response body. Keep request-scoped components out of `persistentComponents`; lazy sessions remain isolated
when `useSession=false`. See [configuration](docs/configuration.md) and [worker examples](docs/examples.md).

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

    # request hints for frameworks that consume X-Sendfile-Type and X-Accel-Mapping
    # see: https://github.com/php/frankenphp/blob/main/docs/x-sendfile.md
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

For Yii, select the response header explicitly. The URI is relative to the interceptor's `private-files/` root:

```php
return Yii::$app->response->xSendFile('/report.pdf', 'report.pdf', ['xHeader' => 'X-Accel-Redirect']);
```

This serves `private-files/report.pdf`. Authorize access and select the file on the server before returning the
response. Yii's default `X-Sendfile` response header is not matched by this interceptor.

### Standalone Binary

Install a worker-capable binary following the [official FrankenPHP instructions](https://frankenphp.dev/docs/),
then run from the application root:

```bash
frankenphp run --config Caddyfile --watch
```

On Windows, use [WSL](https://learn.microsoft.com/windows/wsl/).

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

Use `yii2\extensions\psrbridge\http\UploadedFile` in the application's upload model. Validate file content and size,
use a server-generated storage name, and check `saveAs()` before reporting success. See
[uploads and streaming downloads](docs/examples.md#uploads-and-streaming-downloads).

## Documentation

Practical guides and complete worker examples for FrankenPHP and request
isolation.

- 📚 [Installation Guide](docs/installation.md)
- ⚙️ [Configuration Guide](docs/configuration.md)
- 💡 [Worker Examples](docs/examples.md)
- 🔄 [Upgrade Guide](UPGRADE.md) and [Migration Checks](docs/migration.md)
- 🧪 [Testing Guide](docs/testing.md)

## AI assistance

AI agents: use the repository guides and source for the installed revision.
For a [documentation deployment](https://yii2-framework.github.io/docs/) with
`llms.txt`, read that index first. When Markdown export is enabled, append
`index.md` to a page URL. For Yii conventions, use the `yii2-framework` skill
from a local framework documentation checkout.

- 🤖 [yii2-frankenphp-worker skill](docs/agents.md): factories and isolation.

If the hosted sources are unavailable, use the repository guides and locally
available skills.

## Package information

[![PHP](https://img.shields.io/badge/%3E%3D8.3-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/releases/8.3/en.php)
[![Yii 2.0.55+](https://img.shields.io/badge/%3E%3D2.0.55-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white)](https://github.com/yiisoft/yii2/releases/tag/2.0.55)
[![Yii 22.0.x](https://img.shields.io/badge/22.0.x-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white)](https://github.com/yiisoft/yii2/tree/22.0)
[![Latest Stable Version](https://img.shields.io/packagist/v/yii2-extensions/franken-php.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Stable)](https://packagist.org/packages/yii2-extensions/franken-php)
[![Total Downloads](https://img.shields.io/packagist/dt/yii2-extensions/franken-php.svg?style=for-the-badge&logo=composer&logoColor=white&label=Downloads)](https://packagist.org/packages/yii2-extensions/franken-php)

## Quality code

[![Codecov](https://img.shields.io/codecov/c/github/yii2-extensions/franken-php.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Coverage)](https://codecov.io/github/yii2-extensions/franken-php)
[![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-4F5D95.svg?style=for-the-badge&logo=php&logoColor=white)](https://github.com/yii2-extensions/franken-php/actions/workflows/static.yml)
[![Quality](https://img.shields.io/github/actions/workflow/status/yii2-extensions/franken-php/quality.yml?style=for-the-badge&label=Quality&logo=github)](https://github.com/yii2-extensions/franken-php/actions/workflows/quality.yml)
[![StyleCI](https://img.shields.io/badge/StyleCI-Passed-44CC11.svg?style=for-the-badge&logo=styleci&logoColor=white)](https://github.styleci.io/repos/1031393416?branch=main)

## Our social networks

[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/Terabytesoftw)

## License

[![License](https://img.shields.io/badge/License-BSD--3--Clause-brightgreen.svg?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=555555)](LICENSE)
