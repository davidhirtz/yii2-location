<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Controllers;

use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\LocationTag;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Test\Fixtures\Traits\LocationFixtureTrait;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Test\Traits\FunctionalTestTrait;
use yii\caching\Dependency;

class ApiControllerFunctionalTest extends TestCase
{
    use FunctionalTestTrait;
    use LocationFixtureTrait;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testIndex(): void
    {
        $this->open('api/location/index.json');
        self::assertResponseIsSuccessful();

        $data = $this->getJsonResponseData();

        self::assertCount(4, $data);
        self::assertArrayHasKey('type', $data[0]);
    }

    public function testIndexWithTypeSlug(): void
    {
        $this->open('api/location/test.json');
        self::assertResponseIsSuccessful();

        $data = $this->getJsonResponseData();

        self::assertCount(2, $data);
        self::assertArrayHasKey('type', $data[0]);
    }

    public function testAnUnpublishedTagFiltersNothing(): void
    {
        Dependency::resetReusableData();
        Location::getModule()->enableTags = true;

        $tag = Tag::create();
        $tag->loadDefaultValues();
        $tag->name = 'Hidden';
        $tag->status = Tag::STATUS_DISABLED;
        self::assertTrue($tag->insert(), print_r($tag->getErrors(), true));

        $locationTag = LocationTag::create();
        $locationTag->populateLocationRelation(Location::findOne(1));
        $locationTag->populateTagRelation($tag);
        self::assertTrue($locationTag->insert(), print_r($locationTag->getErrors(), true));

        TagCollection::invalidateCache();

        $this->open("api/location/index.json?tag=$tag->id");
        self::assertSame([], $this->getJsonResponseData());

        $tag->updateAttributes(['status' => Tag::STATUS_ENABLED]);
        TagCollection::invalidateCache();

        $this->open("api/location/index.json?tag=$tag->id&nocache=1");
        self::assertCount(1, $this->getJsonResponseData());
    }

    public function testIndexCacheVariesOnType(): void
    {
        // A reusable dependency memoises its data in a static that outlives the previous test's cache.
        Dependency::resetReusableData();

        $this->open('api/location/index.json?type=999');
        self::assertCount(0, $this->getJsonResponseData());

        $this->open('api/location/index.json');
        self::assertCount(4, $this->getJsonResponseData());
    }
}
