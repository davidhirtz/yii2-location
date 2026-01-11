<?php

declare(strict_types=1);

namespace Hirtz\Location\Test\Fixtures;

use Hirtz\Location\Models\Location;
use yii\test\ActiveFixture;

class LocationFixture extends ActiveFixture
{
    public $modelClass = Location::class;
}
