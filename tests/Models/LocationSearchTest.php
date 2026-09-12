<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Models;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Location\Test\Fixtures\LocationFixture;
use Hirtz\Location\Test\Fixtures\Traits\LocationFixtureTrait;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Models\Search;
use Hirtz\Skeleton\Models\User;
use Hirtz\Skeleton\Test\Fixtures\UserFixture;
use Override;
use Yii;

class LocationSearchTest extends TestCase
{
    use LocationFixtureTrait;
    use ModuleTrait;

    /**
     * Declared rather than merged from both fixture traits: their `fixtures()` carries `#[Override]`, and an
     * aliased copy of it is a fatal.
     */
    #[Override]
    public function fixtures(): array
    {
        return [
            'location' => LocationFixture::class,
            'user' => UserFixture::class,
        ];
    }

    public function testALocationIsIndexedWithItsAddress(): void
    {
        $location = $this->getLocationFromFixture('location-1');
        $document = $location->getSearchDocuments()[0];

        self::assertSame('Test Location 1', $document->title);
        self::assertSame(0.6, $document->weight);
        self::assertStringContainsString('123 Main St, Test City 12345, US', $document->content);

        $document = $this->getLocationFromFixture('location-2')->getSearchDocuments()[0];
        self::assertStringContainsString('Cupertino', $document->content);
    }

    public function testTheBehaviorWritesTheDocumentsOnSave(): void
    {
        $location = $this->getLocationFromFixture('location-1');
        $location->name = 'Renamed Location';

        self::assertSame(1, $location->update());

        $documents = Search::find()
            ->where(['model_class' => $location::class, 'model_id' => $location->id])
            ->all();

        self::assertCount($this->getLanguageCount(), $documents);
        self::assertSame('Renamed Location', $documents[0]->title);
    }

    public function testATagIsOnlySearchableWhileTagsAreEnabled(): void
    {
        $tag = Tag::create();
        $tag->name = 'Restaurant';

        self::assertFalse($tag->isSearchable());
        self::assertTrue($tag->save());
        self::assertEmpty($this->findTagDocuments($tag));

        self::getModule()->enableTags = true;
        self::assertTrue($tag->isSearchable());

        $tag->name = 'Cafe';
        self::assertSame(1, $tag->update());

        $documents = $this->findTagDocuments($tag);

        self::assertCount($this->getLanguageCount(), $documents);
        self::assertSame('Cafe', $documents[0]->title);
        self::assertSame(0.5, (float)$documents[0]->weight);

        $this->loginTagEditor();
        self::assertNotNull($tag->getSearchResult());

        self::getModule()->enableTags = false;
        self::assertNull($tag->getSearchResult(), 'A tag stays hidden while tags are disabled.');
    }

    /**
     * @return list<Search>
     */
    private function findTagDocuments(Tag $tag): array
    {
        return Search::find()
            ->where(['model_class' => $tag::class, 'model_id' => $tag->id])
            ->all();
    }

    private function loginTagEditor(): void
    {
        /** @var UserFixture $fixture */
        $fixture = $this->getFixture('user');
        $user = User::findOne($fixture->data['admin']['id']);

        $auth = Yii::$app->getAuthManager();
        $auth->assign($auth->getPermission(Tag::AUTH_TAG_UPDATE), $user->id);

        Yii::$app->getUser()->setIdentity($user);
    }

    private function getLanguageCount(): int
    {
        return count(Yii::$app->getI18n()->getLanguages());
    }
}
