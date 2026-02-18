<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp;

use Throwable;
use yii\web\IdentityInterface;
use yii2\extensions\psrbridge\creator\ServerRequestCreator;
use yii2\extensions\psrbridge\emitter\SapiEmitter;
use yii2\extensions\psrbridge\http\{Application, ServerExitCode};

use function is_numeric;

/**
 * Runs the FrankenPHP worker loop for a Yii PSR bridge application.
 *
 * Usage example:
 * ```php
 * $config = require __DIR__ . '/config/web.php';
 *
 * $runner = new \yii2\extensions\frankenphp\FrankenPHP(
 *     new \yii2\extensions\psrbridge\http\Application($config),
 * );
 *
 * $runner->run();
 * ```
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
    private readonly SapiEmitter $emitter;

    /**
     * ServerRequestCreator for creating PSR-7 ServerRequest instances from global variables.
     */
    private readonly ServerRequestCreator $serverRequestCreator;

    /**
     * Creates a new instance of the {@see FrankenPHP} class.
     *
     * @param Application $app Application instance.
     * @param int|null $maxRequests Maximum number of requests to handle before stopping. If `null`, will try to read
     * from MAX_REQUESTS env var, otherwise defaults to '1000'.
     *
     * @throws Throwable if the emitter or server request creator cannot be instantiated.
     *
     * @phpstan-param Application<IdentityInterface> $app
     */
    public function __construct(
        private readonly Application $app,
        private readonly int|null $maxRequests = null,
    ) {
        $container = $this->app->container();

        $this->emitter = $container->get(SapiEmitter::class);
        $this->serverRequestCreator = $container->get(ServerRequestCreator::class);
    }

    /**
     * Runs the FrankenPHP worker loop for handling HTTP requests.
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
