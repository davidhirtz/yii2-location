<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Modules\Admin\Controllers;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\LocationTag;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Location\Test\Fixtures\LocationFixture;
use Hirtz\Location\Test\Fixtures\Traits\LocationFixtureTrait;
use Hirtz\Location\Test\Models\TestLocation;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Models\User;
use Hirtz\Skeleton\Test\Fixtures\UserFixture;
use Override;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * The location, tag and location-tag pages of the admin.
 */
class LocationAdminTest extends TestCase
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

    public function testTheLocationIndexListsTheLocations(): void
    {
        $this->login();

        $html = Yii::$app->runAction('admin/location/location/index');

        self::assertIsString($html);
        self::assertStringContainsString('Test Location 1', $html);
        self::assertStringContainsString('Test Location 4', $html);
    }

    public function testTheLocationIndexFiltersByStatusAndType(): void
    {
        $this->login();

        $html = Yii::$app->runAction('admin/location/location/index', ['status' => Location::STATUS_DISABLED]);

        self::assertIsString($html);
        self::assertStringContainsString('Test Location 3', $html);
        self::assertStringNotContainsString('Test Location 1', $html);

        $html = Yii::$app->runAction('admin/location/location/index', ['type' => TestLocation::TYPE_TEST]);

        self::assertIsString($html);
        self::assertStringContainsString('Test Location 4', $html);
        self::assertStringNotContainsString('Test Location 1', $html);
    }

    /**
     * The search covers the address, which is what an administrator has at hand.
     */
    public function testTheLocationIndexSearchesTheNameAndTheAddress(): void
    {
        $this->login();

        $html = Yii::$app->runAction('admin/location/location/index', ['q' => 'Cupertino']);

        self::assertIsString($html);
        self::assertStringContainsString('Test Location 2', $html);
        self::assertStringNotContainsString('Test Location 1', $html);

        $html = Yii::$app->runAction('admin/location/location/index', ['q' => 'Location 5']);

        self::assertIsString($html);
        self::assertStringContainsString('Test Location 5', $html);
        self::assertStringNotContainsString('Test Location 2', $html);
    }

    public function testTheLocationIndexIsForbiddenWithoutThePermission(): void
    {
        Yii::$app->getUser()->setIdentity($this->getUserFromFixture('admin'));

        $this->expectException(ForbiddenHttpException::class);
        Yii::$app->runAction('admin/location/location/index');
    }

    public function testALocationIsCreated(): void
    {
        $this->login();

        $response = $this->post('admin/location/location/create', [], [
            'Location' => [
                'status' => Location::STATUS_ENABLED,
                'type' => Location::TYPE_DEFAULT,
                'name' => 'A new location',
                'lat' => '48.1372',
                'lng' => '11.5756',
            ],
        ]);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotNull(Location::findOne(['name' => 'A new location']));
    }

    public function testAFormReloadDoesNotSaveTheLocation(): void
    {
        $this->login();

        $html = $this->post('admin/location/location/create', [], [
            'Location' => [
                'status' => Location::STATUS_ENABLED,
                'type' => Location::TYPE_DEFAULT,
                'name' => 'Not saved',
                'lat' => '48.1372',
                'lng' => '11.5756',
            ],
        ], reload: true);

        self::assertIsString($html);
        self::assertNull(Location::findOne(['name' => 'Not saved']));
    }

    public function testALocationIsUpdated(): void
    {
        $this->login();

        $response = $this->post('admin/location/location/update', ['id' => 1], [
            'Location' => [
                'status' => Location::STATUS_ENABLED,
                'type' => Location::TYPE_DEFAULT,
                'name' => 'Renamed',
                'lat' => '37.7749',
                'lng' => '-122.4194',
            ],
        ]);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('Renamed', Location::findOne(1)->name);
    }

    public function testAnUnknownLocationIsNotFound(): void
    {
        $this->login();

        $this->expectException(NotFoundHttpException::class);
        Yii::$app->runAction('admin/location/location/update', ['id' => 99999]);
    }

    public function testALocationIsDeleted(): void
    {
        $this->login();

        $response = $this->post('admin/location/location/delete', ['id' => 1]);

        self::assertInstanceOf(Response::class, $response);
        self::assertNull(Location::findOne(1));
    }

    public function testTheLocationDeleteRefusesAGetRequest(): void
    {
        $this->login();

        $this->expectException(MethodNotAllowedHttpException::class);
        Yii::$app->runAction('admin/location/location/delete', ['id' => 1]);
    }

    public function testTheTagIndexListsTheTags(): void
    {
        $this->login();
        $this->createTag('Alpha');
        $this->createTag('Beta');

        $html = Yii::$app->runAction('admin/location/tag/index');

        self::assertIsString($html);
        self::assertStringContainsString('Alpha', $html);
        self::assertStringContainsString('Beta', $html);

        $html = Yii::$app->runAction('admin/location/tag/index', ['q' => 'Alph']);

        self::assertIsString($html);
        self::assertStringContainsString('Alpha', $html);
        self::assertStringNotContainsString('Beta', $html);
    }

    public function testATagIsCreatedUpdatedAndDeleted(): void
    {
        $this->login();

        $response = $this->post('admin/location/tag/create', [], [
            'Tag' => [
                'status' => Tag::STATUS_ENABLED,
                'type' => Tag::TYPE_DEFAULT,
                'name' => 'A new tag',
            ],
        ]);

        self::assertInstanceOf(Response::class, $response);

        $tag = Tag::findOne(['name' => 'A new tag']);
        self::assertNotNull($tag);

        $response = $this->post('admin/location/tag/update', ['id' => $tag->id], [
            'Tag' => [
                'status' => Tag::STATUS_ENABLED,
                'type' => Tag::TYPE_DEFAULT,
                'name' => 'Renamed tag',
            ],
        ]);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('Renamed tag', Tag::findOne($tag->id)->name);

        $response = $this->post('admin/location/tag/delete', ['id' => $tag->id]);

        self::assertInstanceOf(Response::class, $response);
        self::assertNull(Tag::findOne($tag->id));
    }

    public function testTwoTagsCannotShareAName(): void
    {
        $this->login();
        $this->createTag('Alpha');

        $html = $this->post('admin/location/tag/create', [], [
            'Tag' => [
                'status' => Tag::STATUS_ENABLED,
                'type' => Tag::TYPE_DEFAULT,
                'name' => 'Alpha',
            ],
        ]);

        self::assertIsString($html);
        self::assertSame(1, (int)Tag::find()->where(['name' => 'Alpha'])->count());
    }

    public function testTheTagIndexIsForbiddenWithoutThePermission(): void
    {
        Yii::$app->getUser()->setIdentity($this->getUserFromFixture('admin'));

        $this->expectException(ForbiddenHttpException::class);
        Yii::$app->runAction('admin/location/tag/index');
    }

    /**
     * The whole controller is gone while the module says tags are off, not just the links to it.
     */
    public function testTheLocationTagControllerIsNotFoundWhileTagsAreOff(): void
    {
        $this->login();
        self::getModule()->enableTags = false;

        $this->expectException(NotFoundHttpException::class);
        Yii::$app->runAction('admin/location/location-tag/index', ['location' => 1]);
    }

    public function testATagIsAddedToALocationAndRemovedAgain(): void
    {
        $this->login();
        self::getModule()->enableTags = true;

        $tag = $this->createTag('Alpha');

        $response = $this->post('admin/location/location-tag/create', ['location' => 1, 'tag' => $tag->id]);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotNull(LocationTag::findOne(['location_id' => 1, 'tag_id' => $tag->id]));

        $location = Location::findOne(1);

        self::assertSame(1, $location->tag_count);
        self::assertContains($tag->id, (array)$location->tag_ids);
        self::assertSame(1, Tag::findOne($tag->id)->location_count);

        $response = $this->post('admin/location/location-tag/delete', ['location' => 1, 'tag' => $tag->id]);

        self::assertInstanceOf(Response::class, $response);
        self::assertNull(LocationTag::findOne(['location_id' => 1, 'tag_id' => $tag->id]));
        self::assertSame(0, Location::findOne(1)->tag_count);
        self::assertSame(0, Tag::findOne($tag->id)->location_count);
    }

    public function testRemovingATagThatIsNotThereIsNotFound(): void
    {
        $this->login();
        self::getModule()->enableTags = true;

        $tag = $this->createTag('Alpha');

        $this->expectException(NotFoundHttpException::class);
        $this->post('admin/location/location-tag/delete', ['location' => 1, 'tag' => $tag->id]);
    }

    public function testTheLocationTagIndexListsTheTagsOfTheLocation(): void
    {
        $this->login();
        self::getModule()->enableTags = true;

        $this->createTag('Alpha');

        $html = Yii::$app->runAction('admin/location/location-tag/index', ['location' => 1]);

        self::assertIsString($html);
        self::assertStringContainsString('Alpha', $html);
    }

    private function createTag(string $name): Tag
    {
        $tag = Tag::create();
        $tag->loadDefaultValues();
        $tag->name = $name;

        self::assertTrue($tag->insert(), print_r($tag->getErrors(), true));

        return $tag;
    }

    private function post(string $route, array $params = [], array $bodyParams = [], bool $reload = false): mixed
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = Yii::$app->getRequest();
        $request->setBodyParams([...$bodyParams, $request->csrfParam => $request->getCsrfToken()]);

        if ($reload) {
            $request->getHeaders()->set('X-Form-Reload', 'true');
        }

        return Yii::$app->runAction($route, $params);
    }

    private function login(): User
    {
        $user = $this->getUserFromFixture('admin');
        $auth = Yii::$app->getAuthManager();

        foreach ([Location::AUTH_LOCATION, Tag::AUTH_TAG] as $name) {
            $auth->assign($auth->getPermission($name), $user->id);
        }

        Yii::$app->getUser()->setIdentity($user);

        return $user;
    }

    private function getUserFromFixture(string $key): User
    {
        /** @var UserFixture $fixture */
        $fixture = $this->getFixture('user');

        return User::findOne($fixture->data[$key]['id']);
    }
}
