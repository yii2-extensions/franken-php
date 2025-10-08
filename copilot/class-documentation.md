## Class Documentation Guidelines

This guide defines PHPDoc standards for classes and methods within the PHP-Press project, also optimized for tools like
GitHub Copilot.

## Table of Contents
1. General PHPDoc Rules.
2. Documentation Flow.
3. Class Documentation.
4. Method Documentation.
5. Property Documentation.
6. Type Hints and Templates.
7. Exception Documentation.
8. Best Practices.
9. Copilot Optimization Tips.

## General PHPDoc Rules

### Basic Structure
- All documentation must be in English.
- All classes, interfaces, traits, and abstract classes must have a PHPDoc block.
- All `public` and `protected` methods must have PHPDoc.
- Use complete sentences and end with a period.
- Explain **the "why"** behind design decisions.
- Keep it concise but with relevant context.
- Use proper indentation for multi-line descriptions.
- Include `@copyright` and `@license` with `{@see LICENSE}`.
- Document **only what the code actually does**.

### Common Tags
| Tag           | Purpose                                  |
|---------------|------------------------------------------|
| `@param`      | Describes each input parameter           |
| `@return`     | Explains the returned value              |
| `@throws`     | Lists exceptions thrown                  |
| `@template`   | Declares generic templates               |
| `@phpstan-var`| Specifies complex property types         |

## Documentation Flow

1. Brief one-line description.
2. Detailed description with the "why" behind the design.
3. List of key features with bullets.
4. Practical usage examples.
5. References to related classes (`{@see}`).
6. Copyright and license.

## Class Documentation

### Ejemplo para Clases Regulares
path: /core/Src/Router/Route.php

```php
/**
 * Route definition with immutable configuration and matching capabilities.
 *
 * Represents a route with pattern matching, HTTP method restrictions, middleware support, and parameter validation.
 *
 * Routes are created using factory methods for specific HTTP methods and can be configured using immutable setter
 * methods that return new instances with the requested changes.
 *
 * The matching algorithm compares incoming requests against defined route patterns, considering HTTP methods, hostname
 * constraints, path patterns, query parameters, and storing matched parameters for use in actions.
 *
 * Key features.
 * - API versioning with version prefix support (`v1`, `v2`, etc.).
 * - Early validation of regex patterns during route configuration to prevent runtime errors.
 * - Hostname matching with support for exact domains, wildcards, parameter capture, and regex patterns.
 * - HTTP method-specific factory methods (`GET`, `POST`, `PUT`, etc.).
 * - Immutable fluent interface for configuration.
 * - Middleware integration for request processing.
 * - Parameter validation and default values for path segments.
 * - Pattern-based path matching with parameter extraction.
 * - Priority-based route resolution for handling overlapping patterns.
 * - Query parameter validation and extraction with regex patterns.
 *
 * @see \PHPPress\Router\HttpMethod for enum of valid HTTP methods.
 * @see \PHPPress\Router\RouteCollection for collection, lookup, and URL generation.
 * @see \PHPPress\Router\RouteParser for pattern-to-regex conversion and host matching.
 *
 * @copyright Copyright (C) 2023 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
class Route
```

### Ejemplo Abstract class
path: /core/Src/Web/View/Base/AbstractView.php

```php
/**
 * Base class for view components providing template rendering with layouts and themes.
 *
 * Provides a flexible and extensible foundation for rendering view templates in web applications, supporting
 * context-aware view resolution, event-driven rendering lifecycle, and pluggable renderer registration.
 *
 * This class enables layout wrapping, theme-based path resolution, and parameter inheritance for layouts, making it
 * suitable for complex UI scenarios and modular view architectures.
 *
 * Views are processed through registered renderers based on file extension and can be wrapped in layouts with
 * inherited parameters.
 *
 * The rendering process is event-driven, allowing listeners to modify content before and after rendering.
 *
 * Key features.
 * - Context-aware view path resolution using {@see ViewContextInterface}.
 * - Event-driven rendering lifecycle with before/after hooks.
 * - Filesystem abstraction for view file access.
 * - Flexible renderer registration for multiple file extensions.
 * - Layout support with parameter inheritance and optional disabling.
 * - Pluggable and extensible renderer system.
 * - Theme mapping for dynamic view path resolution.
 *
 * @see \PHPPress\Renderer\PHPEngine for PHP renderer.
 * @see \PHPPress\View\ViewContext for path context handling.
 *
 * @copyright Copyright (C) 2023 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
abstract class AbstractView extends Component
```

### Ejemplo para Interfaces
path: /core/Src/View/ViewContextInterface.php

```php
/**
 * Interface for providing view resolution context in templates.
 *
 * Defines the contract for supplying directory context to view renderers, enabling them to resolve relative paths
 * during template processing by referencing the base directory of the current view.
 *
 * This allows templates to reference other views using paths relative to their own location, supporting modular and
 * maintainable UI architectures.
 *
 * This interface is essential for context-aware view resolution, ensuring that renderers can accurately locate and
 * include related templates, layouts, or partials regardless of the rendering entry point.
 *
 * Key features.
 * - Directory context for templates.
 * - Implementation agnostic for flexible renderer integration.
 * - Path normalization support for consistent resolution.
 * - Supports modular and reusable view structures.
 * - View path resolution for relative includes.
 *
 * @copyright Copyright (C) 2023 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
interface ViewContextInterface
```

### Ejemplo para Excepciones
path: /core/Src/Router/Exception/RouteNotFoundException.php

```php
/**
 * Exception thrown when a route can't be found by its identifier.
 *
 * This exception indicates that an attempt to retrieve a named route failed because no route with the specified
 * identifier exists in the route collection. It helps identify missing or misconfigured route definitions during URL
 * generation.
 *
 * The exception uses automatic message prefixing with "Route not found:" for consistent error reporting across the
 * routing system, aiding in quick identification of route definition issues.
 *
 * Thrown in scenarios including.
 * - Dynamic route generation failures.
 * - Group route resolution errors.
 * - Invalid route names.
 * - Missing route definitions.
 * - Route collection corruption.
 *
 * Key features.
 * - Collection state details.
 * - Exception chaining support.
 * - Message enum integration.
 * - Route identifier tracking.
 * - Route suggestion hints.
 *
 * @copyright Copyright (C) 2023 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class RouteNotFoundException extends Exception
```

Enums Message for standardized error messages for Exceptions
path: /core/Src/Router/Exception/Message.php

```php
/**
 * Represents standardized error messages for router exceptions.
 *
 * This enum defines formatted error messages for various error conditions that may occur during router configuration
 * and execution.
 *
 * It provides a consistent and standardized way to present error messages across the routing system.
 *
 * Each case represents a specific type of error, with a message template that can be populated with dynamic values
 * using the {@see \PHPPress\Router\Exception\Message::getMessage()} method.
 *
 * This centralized approach improves the consistency of error messages and simplifies potential internationalization.
 *
 * Key features.
 * - Centralization of an error text for easier maintenance.
 * - Consistent error handling across the routing system.
 * - Integration with specific exception classes.
 * - Message formatting with dynamic parameters.
 * - Standardized error messages for common cases.
 *
 * @copyright Copyright (C) 2023 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
enum Message: string
{
    /**
     * Error when host doesn't match a required pattern.
     *
     * Format: "The passed host '%s' of doesn't match the regexp '%s'"
     */
    case HOST_NOT_MATCHED = 'The passed host \'%s\' of does not match the regexp \'%s\'';

    /**
     * Returns the formatted message string for the error case.
     *
     * Retrieves the raw message string associated with this error case without parameter interpolation.
     *
     * @param string ...$argument Dynamic arguments to insert into the message.
     *
     * @return string Error message string with interpolated arguments.
     *
     * Usage example:
     * ```php
     * throw new RouterNotFoundException(Message::ROUTE_NOT_FOUND->getMessage('/invalid/path'));
     * ```
     */
    public function getMessage(int|string ...$argument): string
    {
        return sprintf($this->value, ...$argument);
    }
}    
```

### Ejemplo para Tests
path: /core/tests/Router/RouteTest.php

```php
/**
 * Test suite for {@see \PHPPress\Router\Route} class functionality and behavior.
 *
 * Verifies routing component's ability to handle various configurations and matching scenarios.
 *
 * These tests ensure routing features work correctly under different conditions and maintain consistent behavior after
 * code changes.
 *
 * The tests validate complex route matching with parameters, middleware handling, and host configuration, which are
 * essential for proper HTTP request routing in the framework.
 *
 * Test coverage.
 * - Host configuration (domain patterns, subdomain support, hostname matching).
 * - HTTP method handling (normalization, case-insensitivity, method restrictions).
 * - Middleware configuration (single, multiple, order preservation).
 * - Parameter handling (encoded paths, URL-encoded values, parameter extraction).
 * - Path matching (exact match, non-matching paths, suffix handling).
 * - Pattern matching (required and optional segments, path matching).
 * - Priority handling (route priority management).
 * - Query parameters (required, optional, validation, type handling).
 * - Route immutability and builder pattern implementation.
 * - Route name validation (valid names, length constraints, complex patterns).
 * - Route parameters (validation, defaults, pattern matching).
 * - Suffix handling and URL normalization.
 * - URL encoding (percent-encoded paths, proper decoding).
 * - Versioning support (version prefixing, version mismatch handling).
 *
 * @see \PHPPress\Tests\Provider\Router\RouteProvider for test case data providers.
 *
 * @copyright Copyright (C) 2023 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Group('router')]
final class RouteTest extends TestCase
```

## Method Documentation

### Constructor
```php
/**
 * Creates a new instance of the {@see \PHPress\Router\RouteCollection} class.
 *
 * @param array $routes Initial routes to populate the collection, indexed by route name.
 *
 * @phpstan-param Route[] $routes
 */
```

### Regular Methods
```php
/**
 * Retrieves all routes in the collection.
 *
 * Provides direct access to the full set of registered routes in their original indexed form.
 *
 * This method is useful for route inspection, debugging, and bulk operations on the entire route collection.
 *
 * @return array Array of all registered routes indexed by name.
 *
 * Usage example:
 * ```php
 * $allRoutes = $collection->all();
 * ```
 *
 * @phpstan-return Route[]
 */
```

### Protected Methods
(Ensure that PHPDoc is as detailed as for `public` methods, especially if they influence behavior.)

## Property Documentation

- Avoid `@var` if the type is already declared in PHP.
- Use `@phpstan-var` for complex arrays.

```php
/**
 * A map of theme names to their corresponding paths.
 * @phpstan-var array<string, string>
 */
private array $themeMap = [];
```

## Type Hints and Templates

```php
/**
 * @template T of FilesystemAdapter
 * @param array<string, mixed> $config
 * @param Collection<int, T> $items
 */
```

```php
/**
 * @param string|array<string> $paths
 * @return string[]|false
 */
```

## Exception Documentation (@throws)

All @throws annotations must be written in English, use complete sentences, and follow a standardized structure for 
consistency and tooling compatibility (for example, static analysis, Copilot, IDEs).

### Structure

```php
@throws ExceptionClass if [specific condition that causes the exception].
```

- Use if clauses to describe when the exception is thrown.
- Avoid vague wording like “an error occurs...” — be clear and precise.
- Use the most specific exception available. Reserve Throwable or RuntimeException for unexpected or generic errors.
- Keep the description generic unless the method’s context requires specificity.

### Standardized Examples

```php
@throws BadRequestException if the request is invalid or can't be processed.
@throws EmptyStackException if the stack is empty when an element is expected.
@throws FilesystemException if the filesystem operation is invalid or fails.
@throws InvalidArgumentException if one or more arguments are invalid, of incorrect type or format.
@throws InvalidConfigException if the configuration is invalid or incomplete.
@throws InvalidDefinitionException if the definition is invalid or improperly configured.
@throws InvalidRouteParameterException if the route parameters are invalid or incomplete.
@throws MiddlewareResolutionException if the middleware handler is invalid or can't be resolved.
@throws NotInstantiableException if a class or service can't be instantiated.
@throws RouteAlreadyExistsException if a route with the same name is already registered.
@throws RouteNotFoundException if the route is not found or undefined.
@throws RuntimeException if a runtime error prevents the operation from completing successfully.
@throws TemplateNotFoundException if the template file is missing or can't be located.
@throws Throwable if an unexpected error occurs during execution.
@throws ViewNotFoundException if the view or associated renderer is missing or unavailable.
@throws WidgetStackException if the stack is empty, or if `end()` is called without a `begin()` call.
matching {@see AbstractBlock::begin()}.
```

## Best Practices

### Precision
- Document only existing functionality.
- Update documentation when changing code.
- Don't document planned or future features.

### Recommended Structure
1. Clear purpose.
2. How it is implemented.
3. List of features.
4. Usage example.
5. Cross-references.

### Bad vs Good Examples
```php
// ❌ Incorrecto
/** Does something */
function doSomething() {}

// ✅ Correcto
/**
 * Loads a config file and parses options.
 *
 * @param string $path Path to config file.
 * @return array Parsed options.
 */
function loadConfig(string $path): array {}
```

## Copilot Optimization Tips
- Use descriptive names for functions and variables.
- Always declare input/output types.
- Keep PHPDoc updated.
- Use template annotations (`@template`, `@phpstan-*`).
- Include practical and realistic usage examples.
- Emphasize the "why" to improve model suggestions.
- Use comments within `php` blocks to explain each step.
