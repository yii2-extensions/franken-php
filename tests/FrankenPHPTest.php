<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests;

use Exception;
use PHPUnit\Framework\Attributes\Group;
use Psr\Http\Message\ResponseFactoryInterface;
use Yii;
use yii\base\Event;
use yii2\extensions\frankenphp\FrankenPHP;
use yii2\extensions\frankenphp\tests\support\stub\HTTPFunctions;
use yii2\extensions\frankenphp\tests\support\TestCase;
use yii2\extensions\psrbridge\exception\{HeadersAlreadySentException, Message};
use yii2\extensions\psrbridge\http\{Response, ServerExitCode};

use function array_fill;
use function getenv;
use function putenv;
use function sprintf;

use const PHP_INT_MAX;

/**
 * Unit tests for the {@see FrankenPHP} worker runtime integration.
 */
#[Group('frankenphp')]
final class FrankenPHPTest extends TestCase
{
    public function testConstructorMaxRequestsOverridesEnvironmentAndDefault(): void
    {
        $_ENV['MAX_REQUESTS'] = '999';

        $customMaxRequests = 5;

        $returnValues = array_fill(0, $customMaxRequests, true);

        HTTPFunctions::setConsecutiveReturnValues($returnValues);

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app, $customMaxRequests);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            'Constructor parameter should override environment variable and default.',
        );
        self::assertSame(
            $customMaxRequests,
            HTTPFunctions::getHandleRequestCallCount(),
            sprintf(
                "Should process exactly '%d' requests when set via constructor.",
                $customMaxRequests,
            ),
        );
    }

    public function testIgnoreUserAbortIsCalledOnceEvenWithMultipleRequests(): void
    {
        $maxRequests = 3;

        $returnValues = array_fill(0, $maxRequests, true);

        HTTPFunctions::setConsecutiveReturnValues($returnValues);

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app, $maxRequests);

        $frankenPHP->run();

        self::assertSame(
            1,
            HTTPFunctions::getIgnoreUserAbortCallCount(),
            "'ignore_user_abort()' should be called exactly once, regardless of number of requests processed.",
        );
        self::assertSame(
            $maxRequests,
            HTTPFunctions::getHandleRequestCallCount(),
            sprintf("Should process exactly '%d' requests.", $maxRequests),
        );
    }

    public function testIgnoreUserAbortIsCalledOnRun(): void
    {
        HTTPFunctions::setConsecutiveReturnValues([true]);

        $app = $this->application();

        // set a very low memory limit to force 'clean()' to return `true`
        $app->setMemoryLimit(1);

        $frankenPHP = new FrankenPHP($app);

        $frankenPHP->run();

        self::assertSame(
            1,
            HTTPFunctions::getIgnoreUserAbortCallCount(),
            "'ignore_user_abort()' should be called exactly once during 'run()'.",
        );

        $ignoreUserAbortValues = HTTPFunctions::getIgnoreUserAbortValues();

        self::assertCount(
            1,
            $ignoreUserAbortValues,
            "Should have exactly one 'ignore_user_abort()' call.",
        );
        self::assertTrue(
            $ignoreUserAbortValues[0] ?? false,
            "'ignore_user_abort()' should be called with 'true' to prevent worker script termination.",
        );
    }

    public function testIgnoreUserAbortSettingIsEnabled(): void
    {
        HTTPFunctions::setConsecutiveReturnValues([true]);

        $app = $this->application();

        // set a very low memory limit to force 'clean()' to return `true`
        $app->setMemoryLimit(1);

        $frankenPHP = new FrankenPHP($app);

        // initially, 'ignore_user_abort()' should be disabled ('0')
        self::assertSame(
            0,
            HTTPFunctions::getIgnoreUserAbortSetting(),
            "'ignore_user_abort()' should initially be disabled.",
        );

        $frankenPHP->run();

        // after 'run()', 'ignore_user_abort()' should be enabled ('1')
        self::assertSame(
            1,
            HTTPFunctions::getIgnoreUserAbortSetting(),
            "'ignore_user_abort()' should be enabled after calling 'run()'.",
        );
    }

    public function testRunMethodCleansResponseLifecycleWhenEmitFails(): void
    {
        $afterSendEvents = [];

        Event::on(
            Response::class,
            Response::EVENT_AFTER_SEND,
            static function () use (&$afterSendEvents): void {
                $afterSendEvents[] = 'afterSend';
            },
        );

        HTTPFunctions::set_headers_sent(true, __FILE__, __LINE__);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/site/index';
        $_SERVER['HTTP_HOST'] = 'localhost';

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        try {
            $frankenPHP->run();

            self::fail(
                'Expected headers already sent exception.',
            );
        } catch (HeadersAlreadySentException) {
            Event::trigger(Response::class, Response::EVENT_AFTER_SEND);

            self::assertSame(
                [],
                $afterSendEvents,
                'FrankenPHP should clean lifecycle handlers without firing after-send when emit fails.',
            );
            self::assertFalse(
                $app->response->isSent,
                'FrankenPHP should not mark the Yii response sent when PSR emission fails.',
            );
            self::assertSame(
                [false],
                $app->finalizations,
                'Failed emission must finalize exactly once.',
            );
        }
    }

    public function testRunMethodCompletesResponseLifecycleAfterEmit(): void
    {
        $afterSendEvents = [];

        Event::on(
            Response::class,
            Response::EVENT_AFTER_SEND,
            static function () use (&$afterSendEvents): void {
                $afterSendEvents[] = 'afterSend';
            },
        );

        HTTPFunctions::setConsecutiveReturnValues([true]);

        $app = $this->application();

        $app->setMemoryLimit(1);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::OK->value,
            $frankenPHP->run(),
            "FrankenPHP 'run()' method should return 'ServerExitCode::OK' when application is clean.",
        );
        self::assertSame(
            ['afterSend'],
            $afterSendEvents,
            'FrankenPHP should complete Yii response lifecycle after emitting the PSR response.',
        );
    }

    public function testRunMethodEmitsBodyBeforeAfterSend(): void
    {
        $this->expectOutputString('worker-response');

        $outputAtAfterSend = '';

        Event::on(
            Response::class,
            Response::EVENT_AFTER_SEND,
            static function () use (&$outputAtAfterSend): void {
                $outputAtAfterSend = ob_get_contents();
            },
        );

        $app = $this->application();
        $app->responseContent = 'worker-response';

        $app->setMemoryLimit(PHP_INT_MAX);

        (new FrankenPHP($app, 1))->run();

        self::assertSame(
            'worker-response',
            $outputAtAfterSend,
            'Emission must precede after-send.',
        );
        self::assertTrue(
            $app->response->isSent,
            'Successful emission must mark the Yii response sent.',
        );
        self::assertSame(
            [true],
            $app->finalizations,
            'Successful emission must finalize exactly once.',
        );
    }

    public function testRunMethodFinalizesSuccessiveRequests(): void
    {
        $this->expectOutputString('worker-responseworker-response');

        $afterSendCount = 0;

        $app = $this->application(
            [
                'on beforeRequest' => static function () use (&$afterSendCount): void {
                    Event::on(
                        Response::class,
                        Response::EVENT_AFTER_SEND,
                        static function () use (&$afterSendCount): void {
                            $afterSendCount++;
                        }
                    );
                },
            ],
        );
        $app->responseContent = 'worker-response';

        $app->setMemoryLimit(PHP_INT_MAX);

        (new FrankenPHP($app, 2))->run();

        self::assertSame(
            2,
            $afterSendCount,
            'Each request must run its after-send handler once.',
        );
        self::assertSame(
            [true, true],
            $app->finalizations,
            'Each request must finalize exactly once.',
        );
    }

    public function testRunMethodHandlesStringMaxRequestsCorrectly(): void
    {
        $_ENV['MAX_REQUESTS'] = ' 2 ';

        HTTPFunctions::setConsecutiveReturnValues([true, true]);

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            "FrankenPHP should handle string 'MAX_REQUESTS' correctly with proper casting.",
        );
        self::assertSame(
            2,
            HTTPFunctions::getHandleRequestCallCount(),
            "Should process exactly '2' requests when 'MAX_REQUESTS' is '2'.",
        );
    }

    public function testRunMethodHandlesStringMaxRequestsWithLeadingZerosCorrectly(): void
    {
        $_ENV['MAX_REQUESTS'] = '001';

        HTTPFunctions::setConsecutiveReturnValues([true]);

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            "FrankenPHP should process exactly '1' request when 'MAX_REQUESTS' is string '001' with leading zeros.",
        );
        self::assertSame(
            1,
            HTTPFunctions::getHandleRequestCallCount(),
            "Should call 'frankenphp_handle_request()' exactly once when 'MAX_REQUESTS' is string '001'.",
        );
    }

    public function testRunMethodIgnoresNonNumericMaxRequests(): void
    {
        $_ENV['MAX_REQUESTS'] = 'not-a-number';

        $defaultMaxRequests = FrankenPHP::DEFAULT_MAX_REQUESTS;

        $returnValues = array_fill(0, $defaultMaxRequests, true);

        HTTPFunctions::setConsecutiveReturnValues($returnValues);

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            "FrankenPHP should ignore non-numeric 'MAX_REQUESTS' and use default.",
        );
        self::assertSame(
            $defaultMaxRequests,
            HTTPFunctions::getHandleRequestCallCount(),
            sprintf(
                "Should use default '%d' when 'MAX_REQUESTS' is not numeric.",
                $defaultMaxRequests,
            ),
        );
    }

    public function testRunMethodProcessesFullDefaultMaxRequests(): void
    {
        $defaultMaxRequests = FrankenPHP::DEFAULT_MAX_REQUESTS;

        $returnValues = array_fill(0, $defaultMaxRequests, true);

        HTTPFunctions::setConsecutiveReturnValues($returnValues);

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            sprintf(
                "FrankenPHP should not stop at '%d' requests.",
                $defaultMaxRequests - 1,
            ),
        );
        self::assertSame(
            $defaultMaxRequests,
            HTTPFunctions::getHandleRequestCallCount(),
            sprintf(
                "Should process full '%d' requests, not stop at '%d'.",
                $defaultMaxRequests,
                $defaultMaxRequests - 1,
            ),
        );
    }

    public function testRunMethodReadsMaxRequestsFromProcessEnvironmentWhenEnvSuperglobalIsUnset(): void
    {
        $previous = getenv('MAX_REQUESTS');
        putenv('MAX_REQUESTS=2');

        try {
            $app = $this->application();

            $app->setMemoryLimit(PHP_INT_MAX);

            self::assertSame(
                ServerExitCode::REQUEST_LIMIT->value,
                (new FrankenPHP($app))->run(),
            );
            self::assertSame(
                2,
                HTTPFunctions::getHandleRequestCallCount(),
                'The process environment must supply the limit.',
            );
        } finally {
            putenv($previous === false ? 'MAX_REQUESTS' : 'MAX_REQUESTS=' . $previous);
        }
    }

    public function testRunMethodReturnsOkWhenApplicationIsClean(): void
    {
        HTTPFunctions::setConsecutiveReturnValues([true]);

        $app = $this->application();

        // set a very low memory limit to force 'clean()' to return `true`, current memory usage will always be
        // '>= 90%' of '1' byte
        $app->setMemoryLimit(1);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::OK->value,
            $frankenPHP->run(),
            "FrankenPHP 'run()' method should return 'ServerExitCode::OK' when application is clean.",
        );
        self::assertSame(
            1,
            HTTPFunctions::getHandleRequestCallCount(),
            "'frankenphp_handle_request()' should be called exactly once before cleanup.",
        );
    }

    public function testRunMethodReturnsRequestLimitWhenKeepRunningIsFalse(): void
    {
        HTTPFunctions::setKeepRunning(false);

        $app = $this->application();

        // set a very high memory limit to force 'clean()' to return 'false'
        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            "FrankenPHP 'run()' method should return 'ServerExitCode::REQUEST_LIMIT' when keepRunning is 'false'.",
        );
        self::assertSame(
            1,
            HTTPFunctions::getHandleRequestCallCount(),
            "'frankenphp_handle_request()' should be called exactly once before stopping.",
        );
    }

    public function testRunMethodReturnsRequestLimitWhenMaxRequestsReached(): void
    {
        $_ENV['MAX_REQUESTS'] = '2';

        HTTPFunctions::setConsecutiveReturnValues([true, true]);

        $app = $this->application();

        // set a very high memory limit to force 'clean()' to return 'false', current memory usage will never be
        // '>= 90%' of 'PHP_INT_MAX'
        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            "FrankenPHP 'run()' method should return 'ServerExitCode::REQUEST_LIMIT' when max requests reached.",
        );
        self::assertSame(
            2,
            HTTPFunctions::getHandleRequestCallCount(),
            "'frankenphp_handle_request()' should be called exactly '2' times before reaching limit.",
        );
    }

    public function testRunMethodSuppressesHeadBody(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';

        $this->expectOutputString('');

        $app = $this->application();
        $app->responseContent = 'head-body-must-not-escape';

        $app->setMemoryLimit(PHP_INT_MAX);

        (new FrankenPHP($app, 1))->run();

        self::assertTrue(
            $app->response->isSent,
            'A HEAD response must complete the Yii send lifecycle.',
        );
        self::assertSame(
            [true],
            $app->finalizations,
            'A HEAD response must finalize exactly once.',
        );
    }

    public function testRunMethodUsesDefaultMaxRequests(): void
    {
        $defaultMaxRequests = FrankenPHP::DEFAULT_MAX_REQUESTS;

        $returnValues = array_fill(0, $defaultMaxRequests, true);

        HTTPFunctions::setConsecutiveReturnValues($returnValues);

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            sprintf(
                "FrankenPHP should use default 'MAX_REQUESTS' of exactly '%d'.",
                $defaultMaxRequests,
            ),
        );
        self::assertSame(
            $defaultMaxRequests,
            HTTPFunctions::getHandleRequestCallCount(),
            sprintf(
                "Should process exactly '%d' requests with default 'MAX_REQUESTS' value.",
                $defaultMaxRequests,
            ),
        );
    }

    public function testThrowExceptionWhenAfterSendFailsWithoutFinalizingAgain(): void
    {
        $this->expectOutputString('worker-response');

        $exception = new Exception('After-send failed.');

        Event::on(
            Response::class,
            Response::EVENT_AFTER_SEND,
            static function () use ($exception): void {
                throw $exception;
            },
        );

        $app = $this->application();
        $app->responseContent = 'worker-response';

        $app->setMemoryLimit(PHP_INT_MAX);

        try {
            (new FrankenPHP($app, 1))->run();

            self::fail(
                'The after-send exception must propagate.',
            );
        } catch (Exception $actual) {
            self::assertSame(
                $exception,
                $actual,
                'The original after-send exception must propagate.',
            );
            self::assertSame(
                [true],
                $app->finalizations,
                'After-send failure must not trigger a second finalization.',
            );

            Event::trigger(Response::class, Response::EVENT_AFTER_SEND);
        }
    }

    public function testThrowExceptionWhenHeadersAlreadySent(): void
    {
        // configure headers_sent mock to return true (headers already sent)
        HTTPFunctions::set_headers_sent(true, __FILE__, __LINE__);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/site/index';
        $_SERVER['HTTP_HOST'] = 'localhost';

        $app = $this->application();

        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        $this->expectException(HeadersAlreadySentException::class);
        $this->expectExceptionMessage(
            Message::UNABLE_TO_EMIT_RESPONSE_HEADERS_ALREADY_SENT->getMessage(),
        );

        $frankenPHP->run();
    }

    public function testThrowExceptionWhenRequestProcessingFails(): void
    {
        $testException = new Exception('An error occurred during request processing.');

        HTTPFunctions::setShouldThrowException(true, $testException);

        $app = $this->application();

        $frankenPHP = new FrankenPHP($app);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'An error occurred during request processing.',
        );

        $frankenPHP->run();
    }

    public function testThrowExceptionWhenResponseConversionFails(): void
    {
        $exception = new Exception('Response conversion failed.');
        $app = $this->application();

        Yii::$container->set(
            ResponseFactoryInterface::class,
            static function () use ($exception): never {
                throw $exception;
            }
        );

        try {
            (new FrankenPHP($app, 1))->run();

            self::fail(
                'The response conversion exception must propagate.',
            );
        } catch (Exception $actual) {
            self::assertSame(
                $exception,
                $actual,
                'The original response conversion exception must propagate.',
            );
            self::assertSame(
                [false],
                $app->finalizations,
                'Failed handling must finalize exactly once.',
            );
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        HTTPFunctions::reset();

        $_SERVER['REQUEST_METHOD'] = 'GET';

        // clear MAX_REQUESTS environment variable
        unset($_ENV['MAX_REQUESTS']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        HTTPFunctions::reset();

        // clear MAX_REQUESTS environment variable
        unset($_ENV['MAX_REQUESTS']);
    }
}
