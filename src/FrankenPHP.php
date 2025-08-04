<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp;

use Throwable;
use yii2\extensions\psrbridge\creator\ServerRequestCreator;
use yii2\extensions\psrbridge\emitter\SapiEmitter;
use yii2\extensions\psrbridge\http\{ServerExitCode, StatelessApplication};

use function is_numeric;

/**
 * FrankenPHP worker runtime integration for Yii2 Application.
 *
 * Provides a worker loop for handling HTTP requests using FrankenPHP, enabling efficient request processing and
 * seamless interoperability with PSR-7 compatible HTTP stacks in Yii2 Application.
 *
 * This class manages the request lifecycle, including request creation, response emission, and application cleanup,
 * supporting configurable request limits and graceful shutdown.
 *
 * Key features:
 * - Application cleanup and exit code management for robust worker operation.
 * - PSR-7 ServerRequest creation and SAPI response emission via dependency injection.
 * - Strict type safety and exception propagation for error handling.
 * - Worker loop for FrankenPHP runtime with request limit and keep-alive support.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class FrankenPHP
{
    /**
     * Default maximum number of requests to handle before stopping the worker loop.
     */
    public const DEFAULT_MAX_REQUESTS = 1000;

    /**
     * Emitter for PSR-7 responses to the SAPI.
     */
    private SapiEmitter $emitter;

    /**
     * ServerRequestCreator for creating PSR-7 ServerRequest instances from global variables.
     */
    private ServerRequestCreator $serverRequestCreator;

    /**
     * Creates a new instance of the {@see FrankenPHP} class.
     *
     * @param StatelessApplication $app Stateless Application instance.
     * @param int|null $maxRequests Maximum number of requests to handle before stopping. If `null`, will try to read
     * from `MAX_REQUESTS` env var, otherwise defaults to `1000`.
     *
     * @throws Throwable if the emitter or server request creator cannot be instantiated.
     */
    public function __construct(
        private readonly StatelessApplication $app,
        private readonly int|null $maxRequests = null,
    ) {
        $container = $this->app->container();

        $this->emitter = $container->get(SapiEmitter::class);
        $this->serverRequestCreator = $container->get(ServerRequestCreator::class);
    }

    /**
     * Runs the FrankenPHP worker loop for handling HTTP requests.
     *
     * Processes incoming HTTP requests using FrankenPHP, creating PSR-7 ServerRequest instances, emitting responses,
     * and managing application cleanup and exit codes.
     *
     * The worker loop continues until the request limit is reached or a shutdown signal is received.
     *
     * @throws Throwable if an exception occurs during request handling or response emission.
     *
     * @return int Exit code indicating the result of the worker loop execution ({@see ServerExitCode::OK} for success,
     * {@see ServerExitCode::REQUEST_LIMIT} for request limit reached).
     */
    public function run(): int
    {
        // prevent worker script termination when a client connection is interrupted
        ignore_user_abort(true);

        $app = $this->app;
        $emitter = $this->emitter;
        $serverRequestCreator = $this->serverRequestCreator;

        $handler = static function () use ($app, $emitter, $serverRequestCreator): void {
            try {
                $response = $app->handle($serverRequestCreator->createFromGlobals());
                $emitter->emit($response);
            } catch (Throwable $e) {
                throw $e;
            }
        };

        $maxRequests = $this->resolveMaxRequests();

        $requestCount = 0;

        while (true) {
            $keepRunning = frankenphp_handle_request($handler);

            $requestCount++;

            if ($this->app->clean()) {
                return ServerExitCode::OK->value;
            }

            if ($requestCount >= $maxRequests || $keepRunning === false) {
                return ServerExitCode::REQUEST_LIMIT->value;
            }
        }
    }

    /**
     * Resolves the maximum number of requests to handle before stopping the worker loop.
     *
     * Determines the request limit by checking the constructor argument, the 'MAX_REQUESTS' environment variable, or
     * falling back to the default value if neither is set.
     *
     * This method ensures that the worker loop operates with a configurable request limit, supporting both explicit
     * configuration and environment-based overrides for flexible deployment scenarios.
     *
     * @return int Maximum number of requests to process before stopping the worker loop.
     */
    private function resolveMaxRequests(): int
    {
        if ($this->maxRequests !== null) {
            return $this->maxRequests;
        }

        if (isset($_ENV['MAX_REQUESTS']) && is_numeric($_ENV['MAX_REQUESTS'])) {
            return (int) $_ENV['MAX_REQUESTS'];
        }

        return self::DEFAULT_MAX_REQUESTS;
    }
}
