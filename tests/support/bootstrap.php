<?php

declare(strict_types=1);

error_reporting(-1);

defined('YII_DEBUG') || define('YII_DEBUG', true);
define('YII_ENV', 'test');

// root directory of the project
$rootDir = dirname(__DIR__, 2);

// require composer autoloader if available
require "{$rootDir}/vendor/autoload.php";
require "{$rootDir}/vendor/yiisoft/yii2/Yii.php";
