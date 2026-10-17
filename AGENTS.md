# FrankenPHP repository guide

This package is `yii2-extensions/franken-php`, namespace `yii2\extensions\frankenphp`. Read the Git diff,
`composer.json`, `src/FrankenPHP.php`, and relevant tests before editing. Preserve existing staged and unstaged work.
The adapter and PSR bridge must be updated together: the runner requires `Application::finalize()`. PHP is >=8.3;
Yii is `~2.0.55 || ^22.0@dev`. Verify compatibility before changing these constraints.

The runner owns request creation, bridge handling, SAPI emission, and one finalization attempt in `finally`.
Suppress HEAD bodies, finalize with `false` on failure, and let after-send errors propagate without a second call.
Keep streams open through emission. Do not move request-sensitive services into persistent components or bypass
bridge session synchronization. `MAX_REQUESTS` is read before the request loop.

Public documentation is English. Preserve the README logo, badges, and organization sections. Keep installation
concise and migration actions in `UPGRADE.md` and `docs/migration.md`. Append CHANGELOG entries at the end of the
current version section. Link PHPDoc to the README quick start instead of duplicating its usage example.

`skills/yii2-frankenphp-worker/` is the canonical public skill. Discovery links in `.agents/skills/` and
`.claude/skills/` point there. Public skill helpers must be distributed with the skill; keep evaluation fixtures,
reports, private tools, worker examples, and documentation decisions under ignored, export-excluded `internal/`.

Run checks from `docs/testing.md`, including Markdownlint, Prettier, and EditorConfig on edited documentation.
`composer ecs` and `composer rector` mutate files; use `vendor/bin/ecs check` to verify. Unit tests mock native HTTP
functions and cannot establish actual FrankenPHP compatibility. Report unavailable server tests. Do not install
runtime binaries, change system settings, commit, or publish without authorization.

The documentation site consumes package guides, PHPDoc, and skills. Do not edit shared site navigation, generated
outputs, or another package during this task; give the coordinator the required integration changes.
