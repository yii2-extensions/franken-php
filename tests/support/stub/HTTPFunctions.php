<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests\support\stub;

use Closure;
use Throwable;

/**
 * Mocks system HTTP functions for emitter and header testing with controlled state and inspection.
 *
 * Provides controlled replacements for core PHP HTTP header and response functions to facilitate testing of HTTP
 * emitter and response-related code without actual header output or side effects.
 *
 * This class allows tests to simulate and inspect HTTP header operations, response codes, and output flushing by
 * maintaining internal state and exposing methods to manipulate and query that state.
 *
 * Enables validation of emitter logic, header management, and response code handling in isolation from PHP global
 * state.
 *
 * Key features.
 * - No actual header output or side effects; all state is internal and queryable.
 * - Simulation of {@see frankenphp_handle_request} for controlled handler invocation and return value management.
 * - Simulation of {@see headers_sent}, {@see ob_get_length}, {@see ob_get_level} for output buffer and header state
 *   inspection.
 * - Simulation of {@see ignore_user_abort} for controlled user abort behavior testing.
 * - State reset and configuration for repeatable, isolated test runs.
 * - Tracking of handler calls, handlers, and exception throwing for test scenarios.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class HTTPFunctions
{
    /**
     * Current index for consecutive return values.
     */
    private static int $consecutiveCallIndex = 0;

    /**
     * Configurable return values for consecutive calls.
     *
     * @phpstan-var bool[]
     */
    private static array $consecutiveReturnValues = [];

    /**
     * The exception to throw if shouldThrowException is true.
     */
    private static Throwable|null $exceptionToThrow = null;

    /**
     * Tracks the number of times frankenphp_handle_request was called.
     */
    private static int $handleRequestCallCount = 0;

    /**
     * Stores the handlers passed to frankenphp_handle_request.
     *
     * @var array<int, Closure(): void>
     */
    private static array $handlers = [];

    /**
     * Indicates whether headers have been sent.
     */
    private static bool $headersSent = false;

    /**
     * Tracks the file and line number where headers were sent.
     */
    private static string $headersSentFile = '';

    /**
     * Tracks the line number where headers were sent.
     */
    private static int $headersSentLine = 0;

    /**
     * Tracks the number of times ignore_user_abort was called.
     */
    private static int $ignoreUserAbortCallCount = 0;

    /**
     * Tracks the current ignore_user_abort setting.
     */
    private static int $ignoreUserAbortSetting = 0;

    /**
     * Tracks the values passed to ignore_user_abort calls.
     *
     * @var array<int, bool|null>
     */
    private static array $ignoreUserAbortValues = [];

    /**
     * Controls the return value of frankenphp_handle_request.
     */
    private static bool $keepRunning = true;

    /**
     * Tracks the output buffer length.
     */
    private static int $obLength = 0;

    /**
     * Tracks the output buffer level.
     */
    private static int $obLevel = 0;

    /**
     * Controls whether frankenphp_handle_request should throw an exception.
     */
    private static bool $shouldThrowException = false;

    /**
     * @phpstan-param Closure():void $handler
     */
    public static function frankenphp_handle_request(Closure $handler): bool
    {
        self::$handleRequestCallCount++;
        self::$handlers[] = $handler;

        if (self::$shouldThrowException && self::$exceptionToThrow !== null) {
            throw self::$exceptionToThrow;
        }

        $handler();

        if (self::$consecutiveReturnValues !== []) {
            $returnValue = self::$consecutiveReturnValues[self::$consecutiveCallIndex] ?? true;
            self::$consecutiveCallIndex++;

            return $returnValue;
        }

        return self::$keepRunning;
    }

    public static function getHandleRequestCallCount(): int
    {
        return self::$handleRequestCallCount;
    }

    /**
     * Returns the handlers passed to frankenphp_handle_request.
     *
     * @return array<int, Closure(): void>
     */
    public static function getHandlers(): array
    {
        return self::$handlers;
    }

    public static function getIgnoreUserAbortCallCount(): int
    {
        return self::$ignoreUserAbortCallCount;
    }

    public static function getIgnoreUserAbortSetting(): int
    {
        return self::$ignoreUserAbortSetting;
    }

    /**
     * Returns the values passed to ignore_user_abort calls.
     *
     * @return array<int, bool|null>
     */
    public static function getIgnoreUserAbortValues(): array
    {
        return self::$ignoreUserAbortValues;
    }

    public static function headers_sent(mixed &$file = null, mixed &$line = null): bool
    {
        $file = self::$headersSentFile;
        $line = self::$headersSentLine;

        return self::$headersSent;
    }

    public static function ignore_user_abort(bool|null $value = null): int
    {
        self::$ignoreUserAbortCallCount++;
        self::$ignoreUserAbortValues[] = $value;

        if ($value !== null) {
            self::$ignoreUserAbortSetting = $value ? 1 : 0;
        }

        return self::$ignoreUserAbortSetting;
    }

    public static function ob_get_length(): int|false
    {
        return self::$obLength < 0 ? false : self::$obLength;
    }

    public static function ob_get_level(): int
    {
        return self::$obLevel;
    }

    public static function reset(): void
    {
        self::$consecutiveCallIndex = 0;
        self::$consecutiveReturnValues = [];
        self::$exceptionToThrow = null;
        self::$handleRequestCallCount = 0;
        self::$handlers = [];
        self::$headersSent = false;
        self::$headersSentFile = '';
        self::$headersSentLine = 0;
        self::$ignoreUserAbortCallCount = 0;
        self::$ignoreUserAbortSetting = 0;
        self::$ignoreUserAbortValues = [];
        self::$keepRunning = true;
        self::$obLength = 0;
        self::$obLevel = 0;
        self::$shouldThrowException = false;
    }

    public static function set_headers_sent(bool $value = false, string $file = '', int $line = 0): void
    {
        self::$headersSent = $value;
        self::$headersSentFile = $file;
        self::$headersSentLine = $line;
    }

    /**
     * @phpstan-param bool[] $values
     */
    public static function setConsecutiveReturnValues(array $values): void
    {
        self::$consecutiveReturnValues = $values;
        self::$consecutiveCallIndex = 0;
    }

    public static function setIgnoreUserAbortSetting(int $setting): void
    {
        self::$ignoreUserAbortSetting = $setting;
    }

    public static function setKeepRunning(bool $keepRunning): void
    {
        self::$keepRunning = $keepRunning;
    }

    public static function setObLength(int $length): void
    {
        self::$obLength = $length;
    }

    public static function setObLevel(int $level): void
    {
        self::$obLevel = $level;
    }

    public static function setShouldThrowException(bool $shouldThrow, Throwable|null $exception = null): void
    {
        self::$shouldThrowException = $shouldThrow;
        self::$exceptionToThrow = $exception;
    }
}
