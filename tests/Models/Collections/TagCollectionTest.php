<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Models\Collections;

use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Controllers\McpController;
use Hirtz\Skeleton\Web\Controller;
use Yii;

final class TagCollectionTest extends TestCase
{
    /**
     * The records a request loaded must never reach the next one, so `Bootstrap` drops them — a reset that only ran
     * in the tests would leave a resident application serving them forever.
     */
    public function testTheTagsDoNotOutliveTheApplication(): void
    {
        $tag = Tag::create();
        $tag->name = 'Collected';

        self::assertTrue($tag->save(), print_r($tag->getErrors(), true));

        $loaded = TagCollection::getAll()[$tag->id] ?? null;
        self::assertNotNull($loaded);

        $this->reloadApplication();

        self::assertNotSame($loaded, TagCollection::getAll()[$tag->id] ?? null);
    }

    public function testTheWebsiteListsOnlyPublishedTags(): void
    {
        $tag = Tag::create();
        $tag->name = 'Hidden – Ä';
        $tag->status = Tag::STATUS_DISABLED;
        self::assertTrue($tag->save(), print_r($tag->getErrors(), true));

        Yii::$app->controller = new Controller('page', Yii::$app);
        self::assertArrayNotHasKey($tag->id, TagCollection::getAll(true));

        Yii::$app->controller = new McpController('mcp', Yii::$app);
        self::assertArrayHasKey($tag->id, TagCollection::getAll());
    }
}
