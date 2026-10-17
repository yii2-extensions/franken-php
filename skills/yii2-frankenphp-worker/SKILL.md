---
name: yii2-frankenphp-worker
description: Configure yii2-extensions/franken-php workers in Yii applications, including PSR factory bindings, bridge finalization, HEAD responses, request isolation, and worker recycling.
license: BSD-3-Clause
---

# Integrate a Yii FrankenPHP worker

Use `yii2\extensions\frankenphp\FrankenPHP` with `yii2\extensions\psrbridge\http\Application`. Match the installed
adapter and bridge revisions. Both require PHP 8.3+ and Yii `~2.0.55 || ^22.0@dev`; check the resolved framework's
PHP requirement. `Application::finalize()` is required by this adapter. Keep the application's dependency lockfile.

## Read only the relevant guidance

In a root package checkout, use `composer show --self --format=json` for metadata. For installed dependencies,
resolve the real path and read the configured vendor directory's `composer/installed.php` (`versions[<package>]`):
`install_path`, `pretty_version` and Composer's selected `reference`. Do not blindly prefer `source.reference`
over `dist.reference`. For any live package checkout, including a symlinked path install, inspect its current
`HEAD` and files even when clean; report metadata mismatches and let the actual files govern. Uncommitted changes
have no matching SHA. Otherwise use the selected reference or a verified matching tag only after establishing
its source provenance. Compare `composer.lock`; it may differ from the inspected installation. Identify a
lock-only state when no package files are installed, and resolve dependencies separately. Use hosted pages,
catalogs or snapshots only when their source revision matches.

Resolve the bridge separately. Prefer local `docs/installation.md` for setup, `README.md` for the entry
script, `docs/configuration.md` and `docs/examples.md` for lifecycle, and `docs/migration.md` when upgrading.
For a copied skill or archive without these files, read them at the resolved reference in
<https://github.com/yii2-extensions/franken-php>. If unavailable or offline, inspect local source/PHPDoc and the
changelog, and report the gap.

Read the installed bridge's source/PHPDoc for signatures and its `docs/configuration.md` for sessions and JSON
from SAPI globals. Resolve the native FrankenPHP version separately before consulting the
[official runtime guide](https://frankenphp.dev/docs/worker/).

## Bind factories before starting the worker

Supply a PSR-7/PSR-17 implementation and bind response, server request, stream, and uploaded file factories in
`$config['container']['definitions']`. The bridge applies container configuration during construction; the runner
resolves `ServerRequestCreator` and `SapiEmitter` from it when `run()` starts. Run the entry script in FrankenPHP
worker mode. Composer installs the adapter, not the server. Do not introduce an asset manager for this package.

Disable native PHP session cookie emission before opening sessions. Configure the application's cookie validation
secret, routes, and identity model. Parse environment debug flags as booleans; the string `false` must not enable debug.

## Preserve the runner's lifecycle

The runner owns `handle(request)` -> emission -> `finalize(success)` for every request. It suppresses the HEAD body
and finalizes once in `finally`; after-send fires only after successful emission. Do not add another finalizer or call
Yii `run()`/`Response::send()` from the worker. Let after-send exceptions propagate without retrying finalization.
Keep response streams alive through emission and use the bridge's `http\UploadedFile` for model uploads.

Reuse the application, not request-sensitive components. Keep request, response, error handler, session, and user out
of `persistentComponents`. `useSession=false` skips eager session opening around bootstrap; the bridge still isolates
IDs for lazy session access. Preserve this synchronization in overridden hooks and retain cookie validation for
browser sessions. Never store visitor-specific state in `$_ENV` or persistent services.

The request limit is the constructor argument, then `$_ENV['MAX_REQUESTS']` or `getenv('MAX_REQUESTS')` when the
`$_ENV` entry is absent. Missing or nonnumeric values default to 1000. It is resolved before the loop. Non-positive limits still stop after one request. `clean()` checks 90% of the configured memory threshold;
it does not reset application-specific static state.

## Verify the integration

Run relevant package checks, then exercise successive visitors, missing/tampered cookies, an upload followed by no
upload, GET/HEAD, downloads, and after-send success/failure through FrankenPHP. Distinguish mocked unit success from
native runtime success, installer discovery from client discovery, and skill discovery from activation. Report a
missing runtime or unavailable test instead of simulating its result.
