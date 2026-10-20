# Testing

This package provides a consistent set of [Composer](https://getcomposer.org/) scripts for local validation.

Tool references:

- [Actionlint](https://github.com/rhysd/actionlint) for GitHub Actions workflow linting.
- [Codespell](https://github.com/codespell-project/codespell) for spelling checks.
- [Composer Require Checker](https://github.com/maglnet/ComposerRequireChecker) for dependency definition checks.
- [Easy Coding Standard (ECS)](https://github.com/easy-coding-standard/easy-coding-standard) for coding standards.
- [EditorConfig Checker](https://github.com/editorconfig-checker/editorconfig-checker) for EditorConfig compliance.
- [Gitleaks](https://github.com/gitleaks/gitleaks) for secret scanning.
- [Infection](https://infection.github.io/) for mutation testing.
- [Markdownlint-cli2](https://github.com/DavidAnson/markdownlint-cli2) for Markdown linting.
- [PHPStan](https://phpstan.org/) for static analysis.
- [PHPUnit](https://phpunit.de/) for unit tests.
- [Prettier](https://prettier.io/) for Markdown and YAML formatting checks.
- [Rector](https://github.com/rectorphp/rector) for automated refactoring.
- [Yamllint](https://github.com/adrienverge/yamllint) for YAML linting.
- [Zizmor](https://github.com/zizmorcore/zizmor) for GitHub Actions security audits.

## Automated refactoring (Rector)

Run Rector to apply automated code refactoring. This command modifies files.

```bash
composer rector
```

Preview the refactoring without modifying files.

```bash
composer rector-check
```

## Coding standards (ECS)

Check coding standards without modifying files.

```bash
composer ecs-check
```

Apply coding standard fixes. Review the changes before keeping them.

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

Run mutation testing with PHPStan static analysis enabled.

```bash
composer mutation-static
```

Thresholds, thread count and memory options live in `infection.json5`. CI enforces the same thresholds.

## Repository quality checks

Make the CLI tools used by the shared quality workflow available on `PATH`:
actionlint with its ShellCheck helper, codespell, EditorConfig Checker, markdownlint-cli2, Prettier and
yamllint. Run all checks:

```bash
composer quality
```

Each tool also has its own script:

```bash
composer actionlint
composer codespell
composer editorconfig-checker
composer markdownlint
composer prettier
composer yamllint
```

The linter configuration lives in `.github/linters`, and the Prettier configuration in `.prettierrc.json` and
`.prettierignore`. CI uses the same files.

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

## Workflow security checks

Make zizmor and Gitleaks available on `PATH`. Audit the current workflow files
with the same pedantic persona used by the shared security workflow:

```bash
composer zizmor
```

Scan Git history for secrets with the repository's Gitleaks configuration:

```bash
composer gitleaks
```

Run both checks. Findings make the command fail; inspect them rather than
disabling the checks.

```bash
composer security
```

Zizmor needs GitHub API access for its online audits. If the tool runs offline,
report that limit separately. To request an offline audit explicitly:

```bash
composer zizmor -- --offline
```

Report hosted CI execution and its PHP/OS matrix separately from these local
commands. Keep generated reports in ignored `internal/`.

## Passing extra arguments

Composer scripts support forwarding additional arguments using `--`.

Run PHPUnit with code coverage report generation.

```bash
composer tests -- --coverage-html internal/coverage
```

Run PHPStan with a different memory limit.

```bash
composer static -- --memory-limit=512M
```

## CI matrix and Yii resolution

The `build` workflow runs PHPUnit on PHP 8.3, 8.4, and 8.5 on Ubuntu and Windows with the `intl`, `mbstring` and
`sodium` extensions, once per supported Yii line: the `phpunit` job resolves Yii `22.0.x-dev` and the
`phpunit-compatibility` job resolves Yii `2.0.55` (the minimum supported release). Both jobs change the requirement
with `composer require yiisoft/yii2:<version>` before running the suite. The `mutation test` workflow runs Infection
with PHPStan on PHP 8.3.

To repeat either resolution locally, use separate disposable checkouts so their dependencies and lock files cannot
affect each other:

```bash
composer require yiisoft/yii2:2.0.55 --with-all-dependencies
composer tests
```

Repeat in the other checkout with `yiisoft/yii2:22.0.x-dev`.

## Worker lifecycle tests

`phpunit.xml.dist` loads the package's internal-function mocks. The suite uses the real bridge lifecycle and a
deterministic Yii response. It verifies request limits, HEAD body suppression, emission before after-send,
successive requests, failed emission/conversion, and finalization once when after-send throws.

These tests run under PHP CLI; they do not start FrankenPHP. To verify a deployment, use the
[quick start](../README.md#quick-start) with the installed runtime and run the [migration checks](migration.md).
Check native request reset, session isolation, upload reset, HEAD headers/body, partial-download bytes, and worker
restarts. Record the runtime version and distinguish this evidence from mocked unit results.

## Documentation checks

Run the format tools over the guides and skill files after editing them:

```bash
composer markdownlint
composer prettier
composer editorconfig-checker
```

[Back to documentation](../README.md#documentation)
