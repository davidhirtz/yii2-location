<?php

declare(strict_types=1);

namespace Hirtz\Location\Test\Fixtures\Traits;

use Hirtz\Location\Test\Fixtures\LocationFixture;
use Hirtz\Location\Test\Models\TestLocation;
use Override;

trait LocationFixtureTrait
{
    #[Override]
    public function fixtures(): array
    {
        return [
            'location' => LocationFixture::class,
        ];
    }

    protected function getLocationFixture(): LocationFixture
    {
        /** @var LocationFixture $fixture */
        $fixture = $this->getFixture('location');
        return $fixture;
    }

    protected function getLocationFixtureData(string $key): array
    {
        return $this->getLocationFixture()->data[$key];
    }

    protected function getLocationFromFixture(string $key): TestLocation
    {
        return TestLocation::findOne($this->getLocationFixtureData($key)['id']);
    }
}
