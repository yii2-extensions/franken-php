# ChangeLog

## 0.2.0 February 20, 2026

- Bug #45: Rename PSR bridge references `yii2-extensions/psr-bridge` from `StatelessApplication` to `Application` across code and documentation (@terabytesoftw)
- Bug #46: fix(composer): Update `yii2-extensions/psr-bridge` to `0.2.0` in `composer.json` and `README.md` (@terabytesoftw)

## 0.1.2 January 27, 2026

- Bug #39: Add `phpstan` type hints for `StatelessApplication` in `FrankenPHP` and `TestCase` classes (@terabytesoftw)
- Bug #40: Update examples in `testing.md` for running Composer script with arguments (@terabytesoftw)
- Bug #41: Update command syntax in `testing.md` to remove redundant 'run' prefix for Composer scripts (@terabytesoftw)
- Bug #42: Update command syntax in `development.md` and `testing.md` for clarity and consistency (@terabytesoftw)
- Bug #44: Update Rector command in `composer.json` to remove unnecessary 'src' argument (@terabytesoftw)

## 0.1.1 January 25, 2026

- Enh #38: Add `php-forge/coding-standard` to development dependencies for code quality checks and add support `PHP 8.5` (@terabytesoftw)

## 0.1.0 October 8, 2025

- Enh #2: Introduce `FrankenPHP` implementation with tests (@terabytesoftw)
- Bug #3: Add tests for `FrankenPHP` request handling and `MAX_REQUESTS` behavior (@terabytesoftw)
- Bug #4: Add configurable maximum requests handling (@terabytesoftw)
- Bug #5: Update `README.md` and documentation for `FrankenPHP` integration, including installation, configuration, and usage details; remove `examples.md` (@terabytesoftw)
- Bug #6: Prevent worker termination on client connection interruption (@terabytesoftw)
- Bug #7: Add tests for `ignore_user_abort` behavior and ensure it's called correctly (@terabytesoftw)
- Bug #8: Remove cookie and CSRF validation settings from `TestCase` class (@terabytesoftw)
- Bug #9: Remove unused `provide` section and tidy up repository configuration (@terabytesoftw)
- Bug #10: Update `README.md` and configuration documentation for clarity and accuracy (@terabytesoftw)
- Bug #11: Update installation guide to include additional PSR-7/PSR-17 implementation option (@terabytesoftw)
- Bug #12: Add development and debugging instructions for `FrankenPHP` integration (@terabytesoftw)
- Bug #13: Correct wording in the `README.md` (@terabytesoftw)
- Bug #14: Update workflow actions to use `v1` stable version instead of `main`, update `LICENSE.md` (@terabytesoftw)
- Bug #15: Update `infection/infection` version constraint to allow `0.31` (@terabytesoftw)
- Bug #16: Add note to disable PHP automatic session cookie handling in `README.md` (@terabytesoftw)
- Bug #17: Update `README.md` and configuration files to reflect changes from 'public' to 'web' directory structure (@terabytesoftw)
- Bug #18: Correct closing brace in `installation.md` for PHP server configuration (@terabytesoftw)
- Bug #19: Add server start instructions and enhance file upload handling in `README.md` (@terabytesoftw)
- Bug #20: Update badge styles and reorganize sections in `README.md` (@terabytesoftw)
- Bug #21: Change section header from '### Installation' to '## Installation' in `README.md` (@terabytesoftw)
- Bug #22: Update `README.md` to enhance installation instructions and add status badges (@terabytesoftw)
- Bug #23: Correct configuration file path in `README.md` for `FrankenPHP` setup (@terabytesoftw)
- Bug #24: Update `README.md` to remove outdated status badge and reorganize package information section (@terabytesoftw)
- Bug #25: Update `README.md` to correct `Caddyfile` worker path and enhance SSL configuration instructions (@terabytesoftw)
- Bug #26: Add demo section to `README.md` with a link to the live application template (@terabytesoftw)
- Dep #27: Bump `php-forge/actions` from `1` to `2` (@terabytesoftw)
- Bug #28: Update workflows and documentation for improved CI/CD processes and feature clarity (@terabytesoftw)
- Bug #29: Update development status badge to reflect the latest stable version in `README.md` (@terabytesoftw)
- Bug #30: Update `tests/support/bootstrap.php` file path and add `TestCase`` class for improved testing structure (@terabytesoftw)
- Bug #31: Update `.editorconfig` and `.gitignore` for improved consistency and clarity (@terabytesoftw)
- Dep #32: Update `symplify/easy-coding-standard` requirement from `^12.5` to `^13.0` (@dependabot)
- Bug #33: Update SVG dimensions in `features-mobile.svg` for improved layout consistency (@terabytesoftw)
- Bug #34: Update license badge style in `README.md` (@terabytesoftw)
- Bug #36: Update `X-Sendfile` support configuration to `Caddyfile` example in `README.md` (@terabytesoftw)
