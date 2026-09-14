<?php

declare(strict_types=1);

namespace Hirtz\Location\Test\Models;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Types\LocationType;

final class TestLocation extends Location
{
    public const int TYPE_TEST = 2;

    #[\Override]
    public static function getTypes(): array
    {
        return [
            LocationType::make(self::TYPE_DEFAULT)
                ->name('Default')
                ->slug('default'),
            LocationType::make(self::TYPE_TEST)
                ->name('Test')
                ->slug('test'),
        ];
    }
}
