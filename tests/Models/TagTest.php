<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Models;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\LocationTag;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Location\Test\Fixtures\Traits\LocationFixtureTrait;
use Hirtz\Location\Test\TestCase;

class TagTest extends TestCase
{
    use LocationFixtureTrait;
    use ModuleTrait;

    /**
     * The serialized tag used to carry `formatted_address`, `lat` and `lng` — a copy of `Location::fields()`, always
     * null on a tag — while leaving out the tag's own type.
     */
    public function testTheSerializedTagCarriesOnlyItsOwnAttributes(): void
    {
        $tag = Tag::create();
        $tag->name = 'Serialized';

        self::assertTrue($tag->save());
        self::assertSame(['name' => 'Serialized'], $tag->toArray());
    }

    public function testCreateLinkAndDelete(): void
    {
        $tag = Tag::create();
        $tag->name = 'Test Tag';

        self::assertTrue($tag->save());

        $location = $this->getLocationFromFixture('location-1');

        $link = LocationTag::create();
        $link->populateLocationRelation($location);
        $link->populateTagRelation($tag);

        self::assertFalse($link->insert());

        self::getModule()->enableTags = true;

        self::assertTrue($link->save());
        self::assertSame($location->id, $link->location_id);
        self::assertSame(1, $tag->location_count);
        self::assertSame(1, $location->tag_count);
        self::assertContains($tag->id, $location->tag_ids);

        $duplicate = LocationTag::create();
        $duplicate->populateLocationRelation($location);
        $duplicate->populateTagRelation($tag);

        self::assertFalse($duplicate->insert());
        self::assertTrue($tag->delete() === 1);

        $location->refresh();

        self::assertSame(0, $location->tag_count);
        self::assertNull($location->tag_ids);
    }

    /**
     * The tag list is the location's bookkeeping: it is written even where the location no longer validates, and
     * nothing else the in-memory location holds is written with it.
     */
    public function testALinkWritesTheTagsAloneWhateverTheLocationHolds(): void
    {
        self::getModule()->enableTags = true;

        $tag = Tag::create();
        $tag->name = 'Counted';
        self::assertTrue($tag->save());

        $location = $this->getLocationFromFixture('location-1');

        // A country since dropped from the list, say.
        $location->updateAttributes(['country_code' => 'XX']);
        self::assertFalse($location->validate());

        $location->street = 'Never saved';

        $link = LocationTag::create();
        $link->populateLocationRelation($location);
        $link->populateTagRelation($tag);

        self::assertTrue($link->insert(), print_r($link->getErrors(), true));

        $row = Location::findOne($location->id);

        self::assertSame(1, $row->tag_count);
        self::assertSame([$tag->id], $row->tag_ids);
        self::assertNotSame('Never saved', $row->street);
        self::assertNotNull($row->updated_at);
    }
}
