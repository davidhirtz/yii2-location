<?php

declare(strict_types=1);

namespace Hirtz\Location\Test\Models;

use Hirtz\Location\Models\Location;

final class TestLocation extends Location
{
    public const int TYPE_TEST = 2;

    #[\Override]
    public static function getTypes(): array
    {
        return [
            self::TYPE_DEFAULT => [
                'name' => 'Default',
                'slug' => 'default',
            ],
            self::TYPE_TEST => [
                'name' => 'Test',
                'slug' => 'test',
            ],
        ];
    }
}
