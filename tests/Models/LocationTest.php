<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Models;

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

    public function testUnchangedCoordinatesAreNotDirty(): void
    {
        $location = Location::create();
        $location->status = TestLocation::STATUS_ENABLED;
        $location->type = TestLocation::TYPE_TEST;
        $location->lat = 0;
        $location->lng = -74.006;

        self::assertTrue($location->save());
        self::assertSame('0.00000000', $location->lat);

        $location = Location::findOne($location->id);
        self::assertNotNull($location);

        $location->load(['lat' => '0', 'lng' => '-74.006'], '');

        self::assertTrue($location->validate());
        self::assertSame([], $location->getDirtyAttributes());
    }

    public function testInvalidCoordinateIsRejected(): void
    {
        $location = Location::create();
        $location->status = TestLocation::STATUS_ENABLED;
        $location->type = TestLocation::TYPE_TEST;
        $location->lat = 'north';

        self::assertFalse($location->validate());
        self::assertArrayHasKey('lat', $location->getErrors());
    }
}
