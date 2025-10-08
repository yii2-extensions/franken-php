# Documentation Guidelines

This guide defines general documentation standards for non-code content in the PHP-Press project and is designed to work
in tandem with `class-documentation.md` and `unit-test.md`. It is optimized for clarity, maintainability, and
compatibility with GitHub Copilot.

## Table of Contents
1. Writing Style.
2. Technical Writing.
3. Documentation Types.
4. Architecture Docs.
5. Maintenance.
6. Copilot Optimization Tips.

## Writing Style

### Tone and Voice
- Use a **professional but conversational** English tone.
- Write with **clarity and purpose**; prefer **active voice**.
- Address readers directly ("you").
- Explain the **"why"** behind important guidance.

### Microsoft Writing Style Compliance
- Use simple sentence structures (subject + verb + object).
- Keep content concise and conversational.
- Prefer contracted forms (for example, "it's", "you're").
- Use **sentence-style capitalization**.
- Write numbers as numerals (for example, "5 users").
- Format dates as "March 26, 2024".
- Avoid:
  - Sentence fragments
  - Synonyms for a single concept
  - Culturally sensitive terms
  - Long modifier chains
  - Overly complex words

### Formatting Guidelines
- Use headings to organize sections.
- Use whitespace for readability.
- Use bullet points for unordered lists and numbered lists for sequences.
- Keep paragraphs short (3–4 sentences).
- Include serial commas in lists.

## Technical Writing

### Code References
Use backticks \``\` for inline code like:

- `composer.json`
- `DependencyResolver`
- `configure()`
- `$config`
- `PHP_VERSION`
- `['debug' => true]`

### Code Examples
- Use fenced code blocks with language hints.
- Add context with inline comments.
- Keep examples **minimal but complete**.
- Show **input and expected output** when relevant.
- Follow a **consistent coding style**.

**Example:**

```php
// Configure the component with options
$component = new Component(
    [
        'debug' => true,
        'cache' => false,
    ],
);

// Returns: ['status' => 'configured']
$result = $component->getStatus();
```

### Links and References
- Use **relative links** for project docs.
- Use **descriptive link text**.
- Include version numbers for dependencies.
- Link to source code or issues when appropriate.

## Documentation Types

### README Files
- Project overview and purpose
- List of key features
- Quick start guide
- Installation steps
- Usage examples
- Dependencies
- License information

### API Documentation
- Purpose and scope
- Authentication mechanism
- Request and response format
- Error structures and codes
- Rate limiting policies
- Usage examples per endpoint
- Reference to external SDKs or libraries

### Tutorials
- Clear learning objectives
- Prerequisites list
- Step-by-step instructions
- Complete code examples
- Screenshots or expected output
- Troubleshooting tips
- References to deeper docs

### Changelogs
- Follow **semantic versioning**.
- Group entries by type:
  - Added
  - Changed
  - Deprecated
  - Removed
  - Fixed
  - Security
- Provide migration notes if relevant.
- Link to related issues or PRs.

## Architecture Docs

### Component Documentation
- Component purpose and context
- Internal and external dependencies
- Configuration options and defaults
- Usage patterns and lifecycle
- Event triggers and listeners
- Extensibility points (hooks/plugins)
- Performance or scaling considerations

### Integration Guides
- System/environment requirements
- Setup and configuration
- Supported platforms or stacks
- Common scenarios and walkthroughs
- Error handling and logging
- Security best practices
- Links to example projects or templates

## Maintenance

### Version Control
- Keep docs versioned alongside code.
- Submit doc updates as part of pull requests.
- Tag documentation per release.
- Archive obsolete versions.
- Track changes in changelogs or history.

### Quality Checks
- Spellcheck and grammar review
- Validate links and anchors
- Run code snippets if executable
- Refresh outdated screenshots
- Test configuration instructions
- Use consistent formatting across files

### File and Folder Organization
- Match doc folder structure to source tree
- Add index files or README in folders
- Add navigation or sidebar links
- Use version indicators where needed
- Encourage cross-referencing via `{@see}` or relative links

### Feedback and Improvement
- Monitor issue trackers and feedback tools
- Encourage user input for unclear docs
- Update or reword misunderstood sections
- Remove or flag outdated content
- Add examples where readers get stuck

## Copilot Optimization Tips
- Use **clear and consistent terminology**.
- Provide **working, well-commented examples**.
- Declare types when describing parameters or code structure.
- Keep each section purpose-focused (Copilot benefits from separation).
- Avoid speculative language (for example, “might do this”) and favor definitiveness.
- Include real-world usage patterns in documentation.
- Use consistent phrasing so Copilot can detect doc intent.

**Copilot-aligned example:**

```php
// Correct way to initialize the router
$router = new Router();
$router->register(...);
$router->dispatch($request);
```
