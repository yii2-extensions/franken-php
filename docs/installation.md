# Installation guide

## System requirements

- [`PHP`](https://www.php.net/downloads) 8.1 or higher.
- [`Composer`](https://getcomposer.org/download/) for dependency management.
- [`FrankenPHP`](https://github.com/dunglas/frankenphp) 1.2.0 or higher.
- [`OpenSSL`](https://www.openssl.org/) for HTTPS support.
- [`Yii2`](https://github.com/yiisoft/yii2) 2.0.53+ or 22.x.

## Installation

### Method 1: Using [Composer](https://getcomposer.org/download/) (recommended)

Install the extension.

```bash
composer require yii2-extensions/frankenphp:^0.1
```

### Method 2: Manual installation

Add to your `composer.json`.

```json
{
    "require": {
        "yii2-extensions/frankenphp": "^0.1"
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

### Install FrankenPHP binary

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

## Project structure

Organize your project for FrankenPHP:

```text
your-project/
├── public/
│   └── index.php          # FrankenPHP entry point
├── .env                   # Environment variables
├── Caddyfile              # FrankenPHP configuration
├── frankenphp.yaml        # Alternative YAML config (optional)
└── frankenphp             # FrankenPHP binary
```

## Next steps

Once the installation is complete.

- ⚙️ [Configuration Reference](configuration.md)
- 🧪 [Testing Guide](testing.md)
