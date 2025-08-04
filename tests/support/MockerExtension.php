<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests\support;

use Closure;
use PHPUnit\Event\Test\{PreparationStarted, PreparationStartedSubscriber};
use PHPUnit\Event\TestSuite\{Started, StartedSubscriber};
use PHPUnit\Runner\Extension\{Extension, Facade, ParameterCollection};
use PHPUnit\TextUI\Configuration\Configuration;
use Xepozz\InternalMocker\{Mocker, MockerState};
use yii2\extensions\frankenphp\tests\support\stub\HTTPFunctions;

final class MockerExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscribers(
            new class implements StartedSubscriber {
                public function notify(Started $event): void
                {
                    MockerExtension::load();
                }
            },
            new class implements PreparationStartedSubscriber {
                public function notify(PreparationStarted $event): void
                {
                    MockerState::resetState();
                    HTTPFunctions::reset();
                }
            },
        );
    }

    public static function load(): void
    {
        $mocks = [
            [
                'namespace' => 'yii2\extensions\psrbridge\emitter',
                'name' => 'headers_sent',
                'function' => static fn(&$file = null, &$line = null): bool => HTTPFunctions::headers_sent(
                    $file,
                    $line,
                ),
            ],
            [
                'namespace' => 'yii2\extensions\psrbridge\emitter',
                'name' => 'ob_get_level',
                'function' => static fn(): int => HTTPFunctions::ob_get_level(),
            ],
            [
                'namespace' => 'yii2\extensions\psrbridge\emitter',
                'name' => 'ob_get_length',
                'function' => static fn(): int|false => HTTPFunctions::ob_get_length(),
            ],
            [
                'namespace' => 'yii2\extensions\frankenphp',
                'name' => 'frankenphp_handle_request',
                'function' => static fn(Closure $handler): bool => HTTPFunctions::frankenphp_handle_request($handler),
            ],
            [
                'namespace' => 'yii2\extensions\frankenphp',
                'name' => 'ignore_user_abort',
                'function' => static fn(bool|null $value = null): int => HTTPFunctions::ignore_user_abort($value),
            ],
        ];

        $mocker = new Mocker();

        $mocker->load($mocks);

        MockerState::saveState();
    }
}
