<?php

declare(strict_types=1);

namespace Hirtz\Location\Test\Fixtures;

use Hirtz\Location\Models\Location;
use Hirtz\Skeleton\Test\Fixtures\ActiveFixture;

class LocationFixture extends ActiveFixture
{
    public $modelClass = Location::class;
}
