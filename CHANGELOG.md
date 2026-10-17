# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.4.0 October 17, 2026

- fix: complete the Yii/PSR lifecycle; update PHP/Yii and tooling requirements, require stable PSR Bridge 0.4, keep worker examples private, and fix HEAD, worker, skill, docs, and CI.

## 0.3.0 February 28, 2026

- fix: update `FrankenPHP` constructor to `bootstrapContainer()` before worker instantiation.
- fix: move PSR7 resolution to `FrankenPHP::run()` method and remove application container bootstrapping from the runner loop to keep application initialization inside `Application` class.

## 0.2.0 February 20, 2026

- refactor: rename PSR bridge references `yii2-extensions/psr-bridge` from `StatelessApplication` to `Application` across code and documentation.
- build(deps): update `yii2-extensions/psr-bridge` to `0.2.0` in `composer.json`.

## 0.1.2 January 27, 2026

- docs: add `phpstan` type hints for `StatelessApplication` in `FrankenPHP` and `TestCase` classes.
- docs: update examples in `testing.md` for running Composer script with arguments.
- docs: update command syntax in `testing.md` to remove redundant 'run' prefix for Composer scripts.
- docs: update command syntax in `development.md` and `testing.md` for clarity and consistency.
- chore: update Rector command in `composer.json` to remove unnecessary 'src' argument.

## 0.1.1 January 25, 2026

- chore: add `php-forge/coding-standard` to development dependencies for code quality checks and add support `PHP 8.5`.

## 0.1.0 October 8, 2025

- feat: introduce `FrankenPHP` implementation with tests.
- test: add tests for `FrankenPHP` request handling and `MAX_REQUESTS` behavior.
- fix: add configurable maximum requests handling.
- docs: update `README.md` and documentation for `FrankenPHP` integration, including installation, configuration, and usage details; remove `examples.md`.
- fix: prevent worker termination on client connection interruption.
- test: add tests for `ignore_user_abort` behavior and ensure it's called correctly.
- test: remove cookie and CSRF validation settings from `TestCase` class.
- chore: remove unused `provide` section and tidy up repository configuration.
- docs: update `README.md` and configuration documentation for clarity and accuracy.
- docs: update installation guide to include additional PSR-7/PSR-17 implementation option.
- docs: add development and debugging instructions for `FrankenPHP` integration.
- docs: correct wording in the `README.md`.
- ci: update workflow actions to use `v1` stable version instead of `main`, update `LICENSE.md`.
- build(deps): update `infection/infection` version constraint to allow `0.31`.
- docs: add note to disable PHP automatic session cookie handling in `README.md`.
- docs: update `README.md` and configuration files to reflect changes from 'public' to 'web' directory structure.
- docs: correct closing brace in `installation.md` for PHP server configuration.
- docs: add server start instructions and enhance file upload handling in `README.md`.
- docs: update badge styles and reorganize sections in `README.md`.
- docs: change section header from '### Installation' to '## Installation' in `README.md`.
- docs: update `README.md` to enhance installation instructions and add status badges.
- docs: correct configuration file path in `README.md` for `FrankenPHP` setup.
- docs: update `README.md` to remove outdated status badge and reorganize package information section.
- docs: update `README.md` to correct `Caddyfile` worker path and enhance SSL configuration instructions.
- docs: add demo section to `README.md` with a link to the live application template.
- build(deps): bump `php-forge/actions` from `1` to `2`.
- ci: update workflows and documentation for improved CI/CD processes and feature clarity.
- docs: update development status badge to reflect the latest stable version in `README.md`.
- test: update `tests/support/bootstrap.php` file path and add `TestCase` class for improved testing structure.
- chore: update `.editorconfig` and `.gitignore` for improved consistency and clarity.
- build(deps): update `symplify/easy-coding-standard` requirement from `^12.5` to `^13.0`.
- docs: update SVG dimensions in `features-mobile.svg` for improved layout consistency.
- docs: update license badge style in `README.md`.
- docs: update `X-Sendfile` support configuration to `Caddyfile` example in `README.md`.
