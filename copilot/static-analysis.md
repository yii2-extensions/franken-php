# Static Analysis Guidelines

This document defines rules for static code analysis to ensure type safety and detect errors early in the PHP-Press project.

## PHPStan Configuration

### Level Settings
- Use PHPStan level 5 as the minimum requirement.
- Level 8 is mandatory for core components.
- Configure per-directory level settings as needed.

### Base Rules
- Enable strict mode for all files.
- Require explicit method return types.
- Require explicit property types.
- Enable dead code detection.
- Validate template type constraints.

## Type Declarations

### Property Types
```php
/**
 * @var array<string, BundleInterface> Registered bundles indexed by class name.
 */
private array $bundles = [];

/**
 * @var Collection<int, Link> CSS link tags for rendering.
 */
private array $css = [];

/**
 * @var array<class-string<Event>, array<callable>> Event listeners by event class.
 */
private array $listeners = [];
```

### Method Types
```php
/**
 * @template T of object
 * @param class-string<T> $class
 * @param array<string, mixed> $config
 * @return T
 *
 * @throws InvalidArgumentException
 * @throws ContainerException
 */
public function create(string $class, array $config = []): object;
```

### Generic Types
```php
/**
 * @template TKey of array-key
 * @template TValue
 *
 * @param array<TKey, TValue> $items
 * @param callable(TValue): bool $predicate
 * @return array<TKey, TValue>
 */
public function filter(array $items, callable $predicate): array;
```

### Union Types
```php
/**
 * @param array|string|null $paths
 * @return string[]
 *
 * @throws InvalidArgumentException When path is invalid.
 */
public function resolvePaths(array|string|null $paths): array;
```

## PHPStan Baseline

### Managing Baseline
- Generate a baseline for existing issues.
- Review and document accepted issues.
- Update the baseline with each major release.
- Track technical debt in the baseline.

### Baseline Command
```bash
vendor/bin/phpstan analyse --generate-baseline
```

## Custom Rules

### Rule Categories
- Architectural rules.
- Naming conventions.
- Type safety rules.
- Framework-specific rules.

### Rule Implementation
```php
/**
 * @implements Rule<Node\Stmt\Class_>
 */
final class CustomRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\Class_::class;
    }

    /**
     * @param Node\Stmt\Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // Rule implementation.
    }
}
```

## Error Categories

### Type Safety
- Undefined methods/properties.
- Invalid argument types.
- Incompatible return types.
- Missing type declarations.
- Template type mismatches.

### Dead Code
- Unreachable code paths.
- Unused private methods.
- Unused parameters.
- Redundant conditions.
- Dead catch blocks.

### Method Calls
- Unknown method calls.
- Invalid argument counts.
- Type compatibility issues.
- Static call validity.
- Visibility violations.

### Property Access
- Undefined properties.
- Invalid property types.
- Uninitialized properties.
- Readonly violations.
- Visibility checks.

## Best Practices

### Configuration
- Use `phpstan.neon.dist` as the base configuration.
- Include `baseline.neon`.
- Configure for the CI/CD pipeline.
- Set memory limits appropriately.
- Enable result caching.

### Error Handling
- Document intentional suppressions.
- Use ignore patterns sparingly.
- Review suppressions regularly.
- Track technical debt items.
- Fix issues incrementally.

### Performance
- Enable parallel analysis.
- Configure memory limits.
- Use result caching.
- Optimize ignore patterns.
- Analyze incrementally.

### Integration
- Run in the CI/CD pipeline.
- Block merges on errors.
- Generate HTML reports.
- Track error trends.
- Review in PRs.
