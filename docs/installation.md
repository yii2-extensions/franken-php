# Installation guide

Use a [FrankenPHP worker runtime](https://frankenphp.dev/docs/worker/) with PHP 8.3+ and the `filter` and `mbstring`
extensions. The adapter supports Yii `~2.0.55 || ^22.0@dev`. The selected framework and other dependencies may
require a newer PHP version.

## Install the packages

Install the adapter and a PSR-7/PSR-17 implementation:

```bash
composer require yii2-extensions/franken-php:^0.4 httpsoft/http-message
```

Retain the resolved versions in the application's `composer.lock`. See [UPGRADE](../UPGRADE.md) when changing an
existing worker. The adapter has no frontend assets and requires no asset manager.

HttpSoft supplies the factories in the [quick start](../README.md#quick-start). Another PSR-7/PSR-17
implementation can be used by changing all four factory bindings. Composer installs the PHP adapter; obtain the
server separately using the [official FrankenPHP installation instructions](https://frankenphp.dev/docs/).

## Configure the worker

Create `web/index.php` using the [quick start](../README.md#quick-start). It expects the application's `config/web.php`,
including its routes, components, and a secret `request.cookieValidationKey`. Keep cookie and CSRF validation
appropriate to the application; worker mode does not remove their purpose. Set `YII_DEBUG=1` only for development.

Create a `Caddyfile` in the application root:

```caddyfile
{
    frankenphp {
        worker ./web/index.php
    }
}

localhost {
    root * ./web
    php_server
}
```

Run from that root:

```bash
frankenphp run --config Caddyfile
```

For development watching, TLS, Docker, and optional X-Sendfile delivery, see the [README](../README.md) and the
[FrankenPHP configuration reference](https://frankenphp.dev/docs/config/). Pin the runtime version in your deployment.

## Next steps

- [Configuration and lifecycle](configuration.md)
- [Worker examples](examples.md)
- [Migration checks](migration.md)
- [Testing](testing.md)
