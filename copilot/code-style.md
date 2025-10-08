# Code Style Guidelines

This guide defines coding conventions and PHP 8.2 usage for the PHP-Press project. 
It complements `class-documentation.md`, `unit-test.md`, and `documentation.md` to improve readability, maintainability,
and Copilot-assisted development.

## Table of Contents
1. PHP 8.1 Feature Usage.
2. Code Style Rules.
3. Best Practices.
4. Copilot Optimization Tips.

## PHP 8.2 Feature Usage

### Typed Properties with Union Types
Use explicit union types when needed:
```php
private string|int $value;
private readonly DependencyResolver $resolver;
private BundleInterface|null $bundle = null;
```

### Constructor Property Promotion
Prefer concise constructor definitions:
```php
public function __construct(
    private readonly string $name,
    private readonly int $value = 0,
    private readonly array|null $config = null,
) {}
```

### Arrow Functions and Array Operations
Use arrow functions for transformation. Prefer `foreach` for complex logic or side effects.
```php
$transformed = array_map(static fn($x) => $x * 2, $items);
$hasNegative = array_any($items, static fn($x) => $x < 0);
```

### Match Expressions
Use match for explicit value checks:
```php
$result = match ($value) {
    1, 2 => 'low',
    3 => 'medium',
    default => 'high',
};
```
Avoid `match(true)` constructs.

### Named Arguments
Use for disambiguation only:
```php
// Good
$object->configure(options: ['cache' => true], resolver: $resolver);

// Prefer ordered when obvious
$object->configure($resolver, ['cache' => true]);
```

### First-class Callable Syntax
```php
$handler = $object->handle(...);
$callback = strtolower(...);
```

### Enums
Document each case clearly:
```php
enum HttpMethod: string {
    case GET = 'GET'; // Retrieves data
    case POST = 'POST'; // Submits data
    // ...
}
```
## Code Style Rules

### Formatting
- Line length: 120 characters.
- Indentation: 4 spaces.
- PSR-12 compliant.
- Files must end with newline.

### Namespace & Imports
```php
declare(strict_types=1);

namespace PHPPress\Component;

use PHPPress\Event\EventDispatcher;

use function array_map;
use function sprintf;
```
Group `use function` separately after classes.

### Class Structure
- One class per file.
- PSR-4 structure.
- Constants > Properties > Methods.
- Logical grouping.

### Method Design
- Always declare visibility.
- Use return/parameter types.
- Prefer early returns.
- Keep short, focused methods.

### Properties
```php
private readonly EventDispatcherInterface $dispatcher;
private array $options = ['debug' => false];
```
Use `string|null` instead of `?string` for consistency.

### Type Declarations
- Strict types enabled.
- Prefer union with `null` instead of nullable (`?`).
- Use `mixed` sparingly.
- Document complex types in PHPDoc.

### Function Imports
```php
use function array_map;
use function trim;
```
One per line. Alphabetical order.

### Error Handling
- Use specific exceptions.
- Use enums for messages.
- Use `??` operator for fallbacks.
- Document `@throws` in PHPDoc.

### Arrays
```php
$config = [
    'debug'   => true,
    'cache'   => false,
    'version' => '1.0.0',
];
```
Use short syntax. Align `=>` in multiline.

### Control Structures
```php
if ($condition === false) {
    return null;
}
```
- Braces on same line.
- One space after control keywords.
- Use guard clauses.

## Best Practices
- Composition over inheritance.
- Follow SOLID principles.
- Immutable objects when possible.
- Fluent interfaces for configuration.
- Clear and descriptive naming.
- Validate inputs early.

### Immutable + Fluent Pattern
```php
$route = Route::get('users', '/users', $handler)
    ->withParameters(['id' => '\d+'])
    ->withDefaults(['format' => 'json'])
    ->withHost('api.example.com');
```

## Copilot Optimization Tips
- Keep structure and naming predictable.
- Add inline comments to show intent.
- Document "why" in PHPDoc.
- Use examples in comments or docblocks.
- Use type declarations consistently.
- Avoid overloading logic in one line.

**Example for Copilot context:**
```php
// Create a route with constraints
$route = Route::get('profile', '/user/{id}', ProfileController::class)
    ->withDefaults(['id' => '1'])
    ->withParameters(['id' => '\d+']);
```
