<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests;

use Exception;
use PHPUnit\Framework\Attributes\Group;
use yii2\extensions\frankenphp\FrankenPHP;
use yii2\extensions\frankenphp\tests\support\stub\HTTPFunctions;
use yii2\extensions\psrbridge\exception\HeadersAlreadySentException;
use yii2\extensions\psrbridge\http\ServerExitCode;

#[Group('frankenphp')]
final class FrankenPHPTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ob_start();

        HTTPFunctions::reset();

        // clear 'MAX_REQUESTS' environment variable
        unset($_ENV['MAX_REQUESTS']);
    }

    protected function tearDown(): void
    {
        if (ob_get_level() > 1) {
            ob_end_clean();
        }

        parent::tearDown();

        HTTPFunctions::reset();

        // clear 'MAX_REQUESTS' environment variable
        unset($_ENV['MAX_REQUESTS']);
    }

    public function testRunMethodReturnsOkWhenApplicationIsClean(): void
    {
        HTTPFunctions::setConsecutiveReturnValues([true]);

        $app = $this->statelessApplication();

        // set a very low memory limit to force 'clean()' to return 'true', current memory usage will always be
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
            'frankenphp_handle_request should be called exactly once before cleanup.',
        );
    }

    public function testRunMethodReturnsRequestLimitWhenKeepRunningIsFalse(): void
    {
        HTTPFunctions::setKeepRunning(false);

        $app = $this->statelessApplication();

        // set a very high memory limit to force 'clean()' to return 'false'
        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        self::assertSame(
            ServerExitCode::REQUEST_LIMIT->value,
            $frankenPHP->run(),
            "FrankenPHP 'run()' method should return 'ServerExitCode::REQUEST_LIMIT' when keepRunning is false.",
        );
        self::assertSame(
            1,
            HTTPFunctions::getHandleRequestCallCount(),
            'frankenphp_handle_request should be called exactly once before stopping.',
        );
    }

    public function testRunMethodReturnsRequestLimitWhenMaxRequestsReached(): void
    {
        $_ENV['MAX_REQUESTS'] = '2';

        HTTPFunctions::setConsecutiveReturnValues([true, true]);

        $app = $this->statelessApplication();

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
            'frankenphp_handle_request should be called exactly 2 times before reaching limit.',
        );
    }

    public function testThrowExceptionWhenHeadersAlreadySent(): void
    {
        // configure headers_sent mock to return true (headers already sent)
        HTTPFunctions::set_headers_sent(true, __FILE__, __LINE__);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/site/index';
        $_SERVER['HTTP_HOST'] = 'localhost';

        $app = $this->statelessApplication();
        $app->setMemoryLimit(PHP_INT_MAX);

        $frankenPHP = new FrankenPHP($app);

        $this->expectException(HeadersAlreadySentException::class);
        $this->expectExceptionMessage('Unable to emit response; headers already sent.');

        $frankenPHP->run();
    }

    public function testThrowExceptionWhenRequestProcessingFails(): void
    {
        $testException = new Exception('An error occurred during request processing.');

        HTTPFunctions::setShouldThrowException(true, $testException);

        $app = $this->statelessApplication();

        $frankenPHP = new FrankenPHP($app);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('An error occurred during request processing.');

        $frankenPHP->run();
    }
}
