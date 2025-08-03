# Installation guide

## System requirements

- [`PHP`](https://www.php.net/downloads) 8.1 or higher.
- [`Composer`](https://getcomposer.org/download/) for dependency management.
- [`Yii2`](https://github.com/yiisoft/yii2) 2.0.53+ or 22.x.

## Installation

### Method 1: Using [Composer](https://getcomposer.org/download/) (recommended)

Install the extension.

```bash
composer require github_username/github_repository-name
```

### Method 2: Manual installation

Add to your `composer.json`.

```json
{
    "require": {
        "github_username/github_repository-name": "^1.0"
    }
}
```

Then run.

```bash
composer update
```

## Next steps

Once the installation is complete.

- ⚙️ [Configuration Reference](configuration.md)
- 💡 [Usage Examples](examples.md)
- 🧪 [Testing Guide](testing.md)
