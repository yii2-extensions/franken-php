# Upgrade Guide

## 0.4.0

### Runtime and dependency requirements

Upgrade PHP to 8.3 or newer and Yii to `~2.0.55 || ^22.0@dev`. Update the adapter and its PSR Bridge dependency
as shown in [installation](docs/installation.md). The adapter requires
`Application::finalize()`, which the 0.3 bridge lacks.

### Response lifecycle

Remove custom finalization hooks: the runner completes after-send once after successful emission and skips it on
failure. Keep response streams open until emission finishes; do not call Yii `Response::send()` in the worker.

HEAD requests now omit the response body. Update any tests that expected GET bytes on HEAD.

Run the [migration checks](docs/migration.md) for session isolation, uploads, and the worker lifecycle.

## 0.2.0

### Breaking changes

- `yii2\extensions\psrbridge\http\StatelessApplication` was renamed to `yii2\extensions\psrbridge\http\Application`.
- No compatibility alias is provided for `StatelessApplication`; all imports and type hints must be updated.

### Migration steps

#### 1) Update imports and type hints

Replace all references to the old class in constructors, properties, method signatures, and PHPDoc:

```php
use yii2\extensions\psrbridge\http\Application;
```

```php
private Application $app;
```

```php
/** @phpstan-param Application<IdentityInterface> $app */
```

#### 2) Update instantiation sites

Replace imports and instantiation sites:

```php
use yii2\extensions\psrbridge\http\Application;
```

```php
$app = new Application($config);
```
