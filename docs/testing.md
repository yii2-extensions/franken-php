# Testing

This package provides a consistent set of [Composer](https://getcomposer.org/) scripts for local validation.

Tool references:

- [Composer Require Checker](https://github.com/maglnet/ComposerRequireChecker) for dependency definition checks.
- [Easy Coding Standard (ECS)](https://github.com/easy-coding-standard/easy-coding-standard) for coding standards.
- [Infection](https://infection.github.io/) for mutation testing.
- [PHPStan](https://phpstan.org/) for static analysis.
- [PHPUnit](https://phpunit.de/) for unit tests.
- [Rector](https://github.com/rectorphp/rector) for automated refactoring.

## Automated refactoring (Rector)

Run Rector to apply automated code refactoring.

```bash
composer run rector
```

## Coding standards (ECS)

Run Easy Coding Standard (ECS) and apply fixes.

```bash
composer run ecs
```

## Dependency definition check

Verify that runtime dependencies are correctly declared in `composer.json`.

```bash
composer run check-dependencies
```

## Mutation testing (Infection)

Run mutation testing.

```bash
composer run mutation
```

Run mutation testing with static analysis enabled.

```bash
composer run mutation-static
```

## Static analysis (PHPStan)

Run static analysis.

```bash
composer run static
```

## Unit tests (PHPUnit)

Run the full test suite.

```bash
composer run tests
```

## Passing extra arguments

Composer scripts support forwarding additional arguments using `--`.

Example: run a specific PHPUnit test or filter by name.

```bash
composer run tests -- --filter SvgTest
```

Example: run PHPStan with a different memory limit:

```bash
composer run static -- --memory-limit=512M
```
