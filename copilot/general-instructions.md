# General Project Instructions

This document outlines global standards for the PHP-Press project. 
It complements `class-documentation.md`, `unit-test.md`, `documentation.md`, and `code-style.md` to provide a unified
foundation for team workflows, Copilot guidance, and contributor consistency.

## Table of Contents
1. Communication
2. Language & Version
3. Code Organization
4. Quality Assurance
5. Documentation Standards
6. Contribution Workflow
7. Reference Documents

## Communication

- Respond to users **in Spanish**.
- Write all code and documentation **in English**.
- Ask for clarification if a request or requirement is ambiguous.
- Use clear, precise phrasing aligned with the **Microsoft Writing Style Guide**.
- When referencing filesystem paths related to the MCP command system, assume the project root is: `/core`.

## Language & Version

- Use **PHP 8.2** for all backend development.
- Adopt new features such as:
  - Constructor property promotion.
  - Property hooks.
  - Arrow functions.
  - Enum types.
  - Array utility functions (`array_any`, `array_all`, etc.).
- See `code-style.md` for usage patterns and examples.

## Code Organization

- Follow **PSR-4 autoloading** and namespace structure.
- Group components by domain (for example, `Router`, `View`, `Asset`).
- Keep interfaces in `Interface/` or `Contracts/` folders.
- Structure tests to mirror the source tree inside the `/tests/` folder.
- Use the following directory conventions:
  ```text
  /core
    /Src
      /ComponentName
    /tests
      /ComponentName
  ```

---

## Quality Assurance

- Use **PHPUnit** for all unit tests.
- Aim for **100% test coverage**.
- Configure **PHPStan** at **level 5 or higher** (see `static-analysis.md`).
- Apply code style fixes using **ECS**.

> Ensure every pull request includes validation for:
> - Static analysis.
> - Code style.
> - Unit tests passing.

## Documentation Standards

- Follow `class-documentation.md` for PHPDoc in classes, methods, and properties.
- Use `documentation.md` for non-code documentation.
- Add real-world usage examples in class-level docblocks.
- Maintain a structured and complete `CHANGELOG.md`.
- Document exceptions with `@throws` and type declarations.

## Contribution Workflow

- All contributors must:
  - Fork from the `main` branch.
  - Create topic branches per feature/fix.
  - Use **conventional commits** format:
    ```
    type(scope): description.
    ```
    Example: `feat(router): add host validation`.
  - Reference related issues via `#ID` in commits or PR descriptions.
  - Include test coverage for new functionality.
  - Pass all CI checks before requesting review.

---

## Reference Documents

For deeper implementation and writing standards, refer to:

- [`class-documentation.md`](./class-documentation.md) — Class and method PHPDoc rules.
- [`unit-test.md`](./unit-test.md) — Test architecture and PHPUnit conventions.
- [`documentation.md`](./documentation.md) — General writing and formatting rules.
- [`code-style.md`](./code-style.md) — PHP code syntax and 8.2 features.
- [`static-analysis.md`](./static-analysis.md) — PHPStan config, rules, and usage.

