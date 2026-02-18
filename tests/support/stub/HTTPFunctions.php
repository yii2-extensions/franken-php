<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests\support\stub;

use Closure;
use Throwable;

/**
 * Stateful stub for internal HTTP-related functions used by tests.
 *
 * Provides deterministic replacements for function calls consumed by emitter and FrankenPHP integration paths.
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
     * Exception thrown when request handling is configured to fail.
     */
    private static Throwable|null $exceptionToThrow = null;

    /**
     * Number of calls made to `frankenphp_handle_request()`.
     */
    private static int $handleRequestCallCount = 0;

    /**
     * Handlers captured from `frankenphp_handle_request()` calls.
     *
     * @var array<int, Closure(): void>
     */
    private static array $handlers = [];

    /**
     * Whether headers are reported as sent.
     */
    private static bool $headersSent = false;

    /**
     * File reported by `headers_sent()`.
     */
    private static string $headersSentFile = '';

    /**
     * Line reported by `headers_sent()`.
     */
    private static int $headersSentLine = 0;

    /**
     * Number of calls made to `ignore_user_abort()`.
     */
    private static int $ignoreUserAbortCallCount = 0;

    /**
     * Current setting returned by `ignore_user_abort()`.
     */
    private static int $ignoreUserAbortSetting = 0;

    /**
     * Values received by `ignore_user_abort()` calls.
     *
     * @var array<int, bool|null>
     */
    private static array $ignoreUserAbortValues = [];

    /**
     * Default return value for `frankenphp_handle_request()`.
     */
    private static bool $keepRunning = true;

    /**
     * Value returned by `ob_get_length()`.
     */
    private static int $obLength = 0;

    /**
     * Value returned by `ob_get_level()`.
     */
    private static int $obLevel = 0;

    /**
     * Whether `frankenphp_handle_request()` throws the configured exception.
     */
    private static bool $shouldThrowException = false;

    /**
     * Simulates `frankenphp_handle_request()` and records the handler invocation.
     *
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

    /**
     * Returns how many times request handling was invoked.
     */
    public static function getHandleRequestCallCount(): int
    {
        return self::$handleRequestCallCount;
    }

    /**
     * Returns handlers captured from request handling calls.
     *
     * @return array<int, Closure(): void>
     */
    public static function getHandlers(): array
    {
        return self::$handlers;
    }

    /**
     * Returns how many times `ignore_user_abort()` was invoked.
     */
    public static function getIgnoreUserAbortCallCount(): int
    {
        return self::$ignoreUserAbortCallCount;
    }

    /**
     * Returns the current `ignore_user_abort()` setting.
     */
    public static function getIgnoreUserAbortSetting(): int
    {
        return self::$ignoreUserAbortSetting;
    }

    /**
     * Returns values passed to `ignore_user_abort()`.
     *
     * @return array<int, bool|null>
     */
    public static function getIgnoreUserAbortValues(): array
    {
        return self::$ignoreUserAbortValues;
    }

    /**
     * Simulates `headers_sent()` and populates by-reference output arguments.
     */
    public static function headers_sent(mixed &$file = null, mixed &$line = null): bool
    {
        $file = self::$headersSentFile;
        $line = self::$headersSentLine;

        return self::$headersSent;
    }

    /**
     * Simulates `ignore_user_abort()` state changes and returns the current setting.
     */
    public static function ignore_user_abort(bool|null $value = null): int
    {
        self::$ignoreUserAbortCallCount++;
        self::$ignoreUserAbortValues[] = $value;

        if ($value !== null) {
            self::$ignoreUserAbortSetting = $value ? 1 : 0;
        }

        return self::$ignoreUserAbortSetting;
    }

    /**
     * Returns the configured output buffer length, or `false` for negative values.
     */
    public static function ob_get_length(): int|false
    {
        return self::$obLength < 0 ? false : self::$obLength;
    }

    /**
     * Returns the configured output buffer nesting level.
     */
    public static function ob_get_level(): int
    {
        return self::$obLevel;
    }

    /**
     * Resets all stub state to default values.
     */
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

    /**
     * Configures the return values for `headers_sent()`.
     */
    public static function set_headers_sent(bool $value = false, string $file = '', int $line = 0): void
    {
        self::$headersSent = $value;
        self::$headersSentFile = $file;
        self::$headersSentLine = $line;
    }

    /**
     * Configures per-call return values for `frankenphp_handle_request()`.
     *
     * @phpstan-param bool[] $values
     */
    public static function setConsecutiveReturnValues(array $values): void
    {
        self::$consecutiveReturnValues = $values;
        self::$consecutiveCallIndex = 0;
    }

    /**
     * Sets the current `ignore_user_abort()` setting.
     */
    public static function setIgnoreUserAbortSetting(int $setting): void
    {
        self::$ignoreUserAbortSetting = $setting;
    }

    /**
     * Sets the default return value for `frankenphp_handle_request()`.
     */
    public static function setKeepRunning(bool $keepRunning): void
    {
        self::$keepRunning = $keepRunning;
    }

    /**
     * Sets the value returned by `ob_get_length()`.
     */
    public static function setObLength(int $length): void
    {
        self::$obLength = $length;
    }

    /**
     * Sets the value returned by `ob_get_level()`.
     */
    public static function setObLevel(int $level): void
    {
        self::$obLevel = $level;
    }

    /**
     * Configures whether request handling throws and which exception is used.
     */
    public static function setShouldThrowException(bool $shouldThrow, Throwable|null $exception = null): void
    {
        self::$shouldThrowException = $shouldThrow;
        self::$exceptionToThrow = $exception;
    }
}
