# Use the FrankenPHP worker skill

The canonical source is [`skills/yii2-frankenphp-worker/SKILL.md`](../skills/yii2-frankenphp-worker/SKILL.md).
It guides factory binding, native worker setup, bridge finalization, and request isolation.

## Install in an application

### Automatic Composer installation

Complete the [one-time application setup](https://yii2-framework.github.io/docs/project/ai-agents/#composer-installation) to enable `llm/skills`
and set `auto-sync` to `true` in `skills.json`. Approve `yii2-extensions/franken-php` explicitly in
`dependencies.composer.trusted`.
The installed package must include its skill bundle; its `extra.skills.source` declares the directory.

```bash
composer install
composer skills:show
```

With `auto-sync` enabled, later dependency installs and updates synchronize the skill automatically; no per-skill
installer is required. If `auto-sync` is `false`, run `composer skills:update` after dependency installation or updates.

### Optional npx installation

For the optional npx workflow, list the source before copying its skill:

```bash
npx skills@1.7.2 add ./vendor/yii2-extensions/franken-php --list
npx skills@1.7.2 add ./vendor/yii2-extensions/franken-php \
    --skill yii2-frankenphp-worker --agent claude-code codex opencode
```

The source can also be an absolute checkout path. To use GitHub, replace the source with
`yii2-extensions/franken-php` and list first to confirm the selected revision contains the skill. Keep project scope;
`--copy` creates a snapshot that must be refreshed when the package is upgraded.

Composer installation alone does not expose skills under `vendor/` to clients opened at the application root.
For manual setup, copy or link the complete `skills/yii2-frankenphp-worker/` directory to a client discovery path:

| Client                                                | Project path                                                            | Explicit activation                           |
| ----------------------------------------------------- | ----------------------------------------------------------------------- | --------------------------------------------- |
| [Claude Code](https://code.claude.com/docs/en/skills) | `.claude/skills/yii2-frankenphp-worker/SKILL.md`                        | `/yii2-frankenphp-worker`                     |
| [Codex](https://learn.chatgpt.com/docs/build-skills)  | `.agents/skills/yii2-frankenphp-worker/SKILL.md`                        | `$yii2-frankenphp-worker` or the skill picker |
| [OpenCode](https://opencode.ai/docs/skills/)          | `.opencode/skills/yii2-frankenphp-worker/SKILL.md` or `.agents/skills/` | Ask it to load `yii2-frankenphp-worker`       |

The installer uses `.agents/skills/` for Codex and OpenCode, and `.claude/skills/` for Claude Code. Keep one editable
source. This checkout's discovery links point to `skills/`; avoid duplicate installations in a client's search paths.

## Verify installation and use

- **Installation:** verify the directory and YAML frontmatter at the selected client path.
- **Discovery:** open a fresh client session at the application root and check the skill list or picker.
- **Activation:** invoke the skill and verify that its body was loaded. Automatic activation is model-dependent.
- **Consultation:** read the relevant package guide or source signature; avoid loading an entire API catalog.
- **Functional result:** run the [migration checks](migration.md) through FrankenPHP.

Installer `--list` proves installer discovery only. It does not prove native client activation or runtime success.

[Back to documentation](../README.md#documentation)
