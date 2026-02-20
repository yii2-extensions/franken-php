# Installation guide

## System requirements

- [`PHP`](https://www.php.net/downloads) 8.1 or higher.
- [`Composer`](https://getcomposer.org/download/) for dependency management.
- [`FrankenPHP`](https://github.com/dunglas/frankenphp) 1.2.0 or higher.
- [`OpenSSL`](https://www.openssl.org/) for HTTPS support.
- [`Yii2`](https://github.com/yiisoft/yii2) 2.0.53+ or 22.x.

### PSR-7/PSR-17 HTTP Message Factories

Install exactly one of the following PSR-7/PSR-17 HTTP message implementations.

- [`guzzlehttp/psr7`](https://github.com/guzzle/psr7)
- [`httpsoft/http-message`](https://github.com/httpsoft/http-message)
- [`laminas/laminas-diactoros`](https://github.com/laminas/laminas-diactoros)
- [`nyholm/psr7`](https://github.com/Nyholm/psr7)

For example, install HttpSoft (recommended for Yii2 applications).

```bash
composer require httpsoft/http-message
```

## Installation

### Method 1: Using [Composer](https://getcomposer.org/download/) (recommended)

Install the extension.

```bash
composer require yii2-extensions/franken-php:^0.2
```

### Method 2: Manual installation

Add to your `composer.json`.

```json
{
    "require": {
        "yii2-extensions/franken-php": "^0.2"
    }
}
```

Then run.

```bash
composer update
```

### FrankenPHP configuration

Create `Caddyfile` in your project root (worker mode).

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

### Install FrankenPHP binary

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

## Project structure

Organize your project for FrankenPHP:

```text
app-basic/
├── web/
│   └── index.php          # FrankenPHP entry point
├── Caddyfile              # FrankenPHP configuration
├── frankenphp.yaml        # Alternative YAML config (optional)
└── frankenphp             # FrankenPHP binary
```

## Next steps

Once the installation is complete.

- ⚙️ [Configuration Reference](configuration.md)
- 🧪 [Testing Guide](testing.md)
