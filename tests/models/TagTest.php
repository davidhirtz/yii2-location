<?php

declare(strict_types=1);

namespace Hirtz\Location\tests\models;

use Hirtz\Location\Models\LocationTag;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Location\Test\Fixtures\Traits\LocationFixtureTrait;
use Hirtz\Location\Test\TestCase;

class TagTest extends TestCase
{
    use LocationFixtureTrait;
    use ModuleTrait;

    public function testCreateLinkAndDelete(): void
    {
        $tag = Tag::create();
        $tag->name = 'Test Tag';

        self::assertTrue($tag->save());
        self::assertEquals(1, $tag->id);

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
}
