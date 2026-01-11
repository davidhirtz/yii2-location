<?php

declare(strict_types=1);

use Hirtz\Location\Bootstrap;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Test\Models\TestLocation;

$basePath = (getenv('BASE_PATH') ?: getcwd());
$config = require("$basePath/vendor/davidhirtz/yii2-skeleton/config/test.php");

return [
    ...$config,
    'bootstrap' => [
        Bootstrap::class,
    ],
    'container' => [
        'definitions' => [
            Location::class => TestLocation::class,
        ],
    ],
];
