# Unit Test Guidelines

## Table of Contents
1. Test Class Naming.
2. Test Method Naming.  
3. Test Structure (Arrange-Act-Assert).
4. Data Providers.
5. Best Practices.  
6. Copilot Optimization Tips.

## Test Class Naming

### Basic Rules
- **Clarity and Descriptiveness:** The test class name should clearly reflect the class being tested.
- **Namespace Structure:** The test class should follow the same namespace structure as the production class,
  located in the `tests/` directory.
- **Naming Convention:** The class name should match the production class and end with `Test`.
- **Internal Documentation:** Document each test case and make sure to mock the necessary dependencies.

**Example:**
```php
// Class to test in src/AbstractView/View.php
namespace PHPPress\View;

class View { }

// Test class in tests/AbstractView/ViewTest.php
namespace PHPPress\Tests\View;

class ViewTest { }
```

## Test Method Naming

### Recommended Pattern
Use the format `test<Action><Subject><Condition>` for test methods, where:
- **<Action>:** Describes the main action (Render, Throw, Return, etc.).
- **<Subject>:** Indicates the subject under test (View, Layout, etc.).
- **<Condition>:** Specifies the condition or scenario.

**Examples:**
```php
public function testRenderLayoutWithContext(): void { }
public function testRenderViewWithTheme(): void { }
public function testThrowExceptionWhenTemplateInvalid(): void { }
```

### Organization
- Group and order test methods alphabetically.
- Maintain consistent nomenclature within the same test class.

## Test Structure (Arrange-Act-Assert)

Each test should follow the AAA pattern to ensure clarity in the intention and behavior of each test:

```php
public function testRenderWithParametersReplacesPlaceholders(): void
{
    // Arrange: Set up the test object and necessary parameters.
    $view = new View($resolver, $dispatcher);
    $parameters = ['title' => 'Test'];

    // Act: Execute the main action.
    $result = $view->render('index', $parameters);

    // Assert: Validate that the result meets expectations.
    $this->assertStringContainsString(
        'Test',
        $result,
        'Rendered view should contain the title parameter.',
    );
}
```

## Data Providers

### Location and Convention
- Place data providers in the `/tests/Provider/` directory.
- Name the files and classes with a `Provider` suffix (for example, `ConverterCommandProvider`).

### Documentation and Example
Include complete documentation in each data provider, explaining the structure and purpose of the data.

**Example:**
path: /core/tests/Provider/Router/RouteProvider.php

```php
namespace PHPPress\Tests\Provider\Router;

/**
 * Data provider for testing the Router component.
 *
 * Designed to ensure the router component correctly processes all supported configurations and appropriately handles
 * edge cases with proper error messaging.
 *
 * The test data validates complex route configuration scenarios including security-sensitive inputs to prevent
 * potential injection vulnerabilities.
 *
 * The provider organizes test cases with descriptive names for quick identification of failure cases during test
 * execution and debugging sessions.
 *
 * Key features.
 * - Comprehensive test cases for all router features.
 * - Edge case testing for input validation.
 * - Host pattern matching with domain validation.
 * - Named test data sets for clear failure identification.
 * - Parameter pattern validation with regular expressions.
 * - Security scenario testing for potential injection patterns.
 * - URL suffix and pattern matching validation.
 *
 * @copyright Copyright (C) 2024 PHPPress.
 * @license https://opensource.org/license/gpl-3-0 GNU General Public License version 3 or later.
 */
final class RouteProvider
```

**Usage in Tests:**
path: /core/tests/Router/RouteTest.php

```php
#[DataProviderExternal(RouteProvider::class, 'names')]
public function testAcceptRouteNamesWhenValid(string $name): void
{
    $route = Route::to($name, '/test', FactoryHelper::createHandler());

    $this->assertSame($name, $route->name, "Route should accept valid route name: '{$name}'.");
}
```

## Best Practices

### General Principles
- **Independence:** Each test should be independent and not depend on the execution order.
- **Cleanup:** Use `setUp()` and `tearDown()` to clean up global or static states between tests.
- **Complete Coverage:** Test both positive and negative cases, including edge values and exception scenarios.
- **Clear Messages:** Assertion messages should explain what was expected and what was obtained, making it easier to
  identify failures.

**Example of Detailed Assertion:**
```php
$this->assertSame(
    $expected,
    $actual,
    'Route should match when URL parameters meet the expected pattern with multiple parameters.'
);
$this->assertTrue(
    $this->filesystem->fileExists("{$this->dirFrom}/js/script.js"),
    'File \'js/script.js\' should exist in the destination after excluding only the strict subdirectory legacy CSS.',
);
$this->assertTrue(
    $this->filesystem->copyMatching($this->dir, $this->dirFrom),
    'Copy matching should return \'true\' when copying all files recursively without patterns.',
);
```

### Exceptions in Tests
- Document in the PHPDoc the conditions under which an exception is expected to be thrown, including clear examples.

**Example:**
path: /core/tests/Router/RouteTest.php

```php
public function testThrowExceptionWithInvalidVersion(): void
{
    $this->expectException(InvalidRouteParameterException::class);
    $this->expectExceptionMessage(Message::INVALID_ROUTE_VERSION->getMessage('invalid'));

    Route::get('users', '/users', FactoryHelper::createHandler())->withVersion('invalid');
}
```

## Copilot Optimization Tips

- **Clear and Descriptive Names:** Use meaningful names for methods and variables.
- **Type Declaration:** Always specify parameter and return types in your test methods.
- **Updated Documentation:** Keep PHPDoc updated with clear and detailed examples.
- **Code Comments:** Add brief comments explaining each part of the AAA structure in your tests.
- **Explanation of "Why":** Include in the documentation the rationale behind each test so that tools
  like GitHub Copilot can generate suggestions aligned with the design.
