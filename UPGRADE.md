# Upgrade Guide

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
