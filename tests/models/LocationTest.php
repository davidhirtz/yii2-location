<?php

declare(strict_types=1);

namespace Hirtz\Location\tests\models;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Test\Models\TestLocation;
use Hirtz\Location\Test\TestCase;

class LocationTest extends TestCase
{
    public function testCreateAndUpdate(): void
    {
        $location = Location::create();

        self::assertInstanceOf(TestLocation::class, $location);

        $location->status = TestLocation::STATUS_ENABLED;
        $location->type = TestLocation::TYPE_TEST;

        self::assertTrue($location->save());
        self::assertSame(6, $location->id);

        $location->name = 'Test Location';
        $location->formatted_address = '123 Main St, New York, NY 10001, US';

        $location->lat = 40.7128;
        $location->lng = -74.0060;

        $location->street = 'Main St';
        $location->house_number = '123';
        $location->locality = 'New York';
        $location->postal_code = '10001';
        $location->district = 'Manhattan';
        $location->state = 'NY';
        $location->country_code = 'US';

        self::assertTrue($location->update() === 1);

        self::assertSame('Test Location', $location->name);
        self::assertSame('United States', $location->getCountryName());
    }
}
