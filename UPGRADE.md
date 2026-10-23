# Upgrade Guide

## 0.4.0

- Upgrade PHP to 8.3 or later and Yii to `~2.0.55 || ^22.0@dev`.
- Update `yii2-extensions/psr-bridge` to `^0.4` together with this package; the runner requires
  `Application::finalize()`.
- Remove custom finalization hooks and do not call Yii `Response::send()` in the worker; the runner finalizes once after
  emission. Keep response streams open until emission finishes.
- HEAD requests no longer return a body; update tests that expected GET bytes.

See the [migration guide](docs/migration.md) for session isolation, uploads and the worker lifecycle.

## 0.2.0

- Rename `yii2\extensions\psrbridge\http\StatelessApplication` to `yii2\extensions\psrbridge\http\Application` in
  imports, type hints, PHPDoc and instantiations; no alias is provided.
