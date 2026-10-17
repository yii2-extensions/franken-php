# Testing

This package provides a consistent set of [Composer](https://getcomposer.org/) scripts for local validation.

Tool references:

- [Composer Require Checker](https://github.com/maglnet/ComposerRequireChecker) for dependency definition checks.
- [Easy Coding Standard (ECS)](https://github.com/easy-coding-standard/easy-coding-standard) for coding standards.
- [Infection](https://infection.github.io/) for mutation testing.
- [PHPStan](https://phpstan.org/) for static analysis.
- [PHPUnit](https://phpunit.de/) for unit tests.
- [Rector](https://github.com/rectorphp/rector) for automated refactoring.

## Worker lifecycle tests

`phpunit.xml.dist` loads the package's internal-function mocks. The suite uses the real bridge lifecycle and a
deterministic Yii response. It verifies request limits, HEAD body suppression, emission before after-send,
successive requests, failed emission/conversion, and finalization once when after-send throws.

These tests run under PHP CLI; they do not start FrankenPHP. To verify a deployment, use the
[quick start](../README.md#quick-start) with the installed runtime and run the [migration checks](migration.md).
Check native request reset, session isolation, upload reset, HEAD headers/body, partial-download bytes, and worker
restarts. Record the runtime version and distinguish this evidence from mocked unit results.

## Documentation checks

Run the installed format tools over edited guides and skill files:

```bash
markdownlint-cli2 --config .github/linters/.markdown-lint.yml \
    README.md UPGRADE.md AGENTS.md 'docs/*.md' 'skills/**/SKILL.md'
prettier --check README.md UPGRADE.md AGENTS.md 'docs/*.md' 'skills/**/SKILL.md'
editorconfig-checker
```

## Automated refactoring (Rector)

Run Rector to apply automated code refactoring.

```bash
composer rector
```

## Coding standards (ECS)

Run Easy Coding Standard (ECS) and apply fixes.

```bash
composer ecs
```

## Dependency definition check

Verify that runtime dependencies are correctly declared in `composer.json`.

```bash
composer check-dependencies
```

## Mutation testing (Infection)

Run mutation testing.

```bash
composer mutation
```

Run mutation testing with static analysis enabled.

```bash
composer mutation-static
```

## Static analysis (PHPStan)

Run static analysis.

```bash
composer static
```

## Unit tests (PHPUnit)

Run the full test suite.

```bash
composer tests
```

## Passing extra arguments

Composer scripts support forwarding additional arguments using `--`.

Run PHPUnit with code coverage report generation.

```bash
composer tests -- --coverage-html code_coverage
```

Run PHPStan with a different memory limit.

```bash
composer static -- --memory-limit=512M
```
