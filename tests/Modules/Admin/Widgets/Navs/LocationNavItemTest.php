<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Modules\Admin\Widgets\Navs;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationNavItem;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Test\Traits\UserFixtureTrait;

class LocationNavItemTest extends TestCase
{
    use ModuleTrait;
    use UserFixtureTrait;

    public function testItLeadsToTheLocations(): void
    {
        $this->loginWith(Location::AUTH_LOCATION);

        self::assertStringStartsWith('<li class="nav-item"><a class="nav-link" href="/admin/location/location/index">', LocationNavItem::make()->render());
    }

    public function testItLeadsToTheTagsOfAUserWhoMayOnlyOpenThose(): void
    {
        self::getModule()->enableTags = true;
        $this->loginWith(Tag::AUTH_TAG);

        self::assertStringStartsWith('<li class="nav-item"><a class="nav-link" href="/admin/location/tag/index">', LocationNavItem::make()->render());
    }

    private function loginWith(string $permission): void
    {
        $user = $this->getUserFromFixture('admin');
        $this->assignPermission($user->id, $permission);
        $this->getWebUser()->setIdentity($user);
    }
}
