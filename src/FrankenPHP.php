<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp;

use Throwable;
use Yii;
use yii\web\IdentityInterface;
use yii2\extensions\psrbridge\creator\ServerRequestCreator;
use yii2\extensions\psrbridge\emitter\SapiEmitter;
use yii2\extensions\psrbridge\http\{Application, ServerExitCode};

use function getenv;
use function is_numeric;

/**
 * Runs the FrankenPHP worker loop for a Yii PSR bridge application.
 *
 * Creates a PSR request from SAPI globals, emits the response, and finalizes the bridge lifecycle once per request.
 * HEAD responses omit the body. See the README quick start for the entry script and required factory bindings.
 */
final class FrankenPHP
{
    /**
     * Default maximum number of requests to handle before stopping the worker loop.
     */
    public const DEFAULT_MAX_REQUESTS = 1000;

    /**
     * Creates a new instance of the {@see FrankenPHP} class.
     *
     * @param Application $app Application instance.
     * @param int|null $maxRequests Maximum number of requests to handle before stopping. If `null`, will try to read
     * from `$_ENV['MAX_REQUESTS']` or the process environment, otherwise defaults to '1000'.
     *
     * @phpstan-param Application<IdentityInterface> $app
     */
    public function __construct(private readonly Application $app, private readonly int|null $maxRequests = null) {}

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
        $emitter = Yii::$container->get(SapiEmitter::class);
        $serverRequestCreator = Yii::$container->get(ServerRequestCreator::class);

        $handler = static function () use ($app, $emitter, $serverRequestCreator): void {
            $emitted = false;

            try {
                $request = $serverRequestCreator->createFromGlobals();
                $response = $app->handle($request);

                $emitter->emit($response, $request->getMethod() !== 'HEAD');

                $emitted = true;
            } finally {
                $app->finalize($emitted);
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

        $value = $_ENV['MAX_REQUESTS'] ?? getenv('MAX_REQUESTS');

        if (is_numeric($value)) {
            return (int) $value;
        }

        return self::DEFAULT_MAX_REQUESTS;
    }
}
