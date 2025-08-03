<?php

declare(strict_types=1);

namespace yii\template\tests;

use PHPUnit\Framework\TestCase;
use yii\template\Example;

final class ExampleTest extends TestCase
{
    public function testExample(): void
    {
        $example = new Example();

        self::assertTrue($example->getExample());
    }
}
