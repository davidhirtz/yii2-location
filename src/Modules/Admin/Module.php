<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin;

use Hirtz\Cms\Models\Entry;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Interfaces\AutocompleteInterface;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationNavItem;
use Hirtz\Skeleton\Modules\Admin\ModuleInterface;
use Hirtz\Skeleton\Widgets\Navs\Nav;
use Hirtz\Skeleton\Widgets\Panels\Dashboard;
use Hirtz\Skeleton\Widgets\Panels\DashboardItem;
use Yii;

/**
 * @property AutocompleteInterface|null $autocomplete
 * @property \Hirtz\Skeleton\Modules\Admin\Module $module
 */
class Module extends \Hirtz\Skeleton\Base\Module implements ModuleInterface
{
    public function getAutocomplete(): ?AutocompleteInterface
    {
        return $this->get('autocomplete', false);
    }

    public function aside(Nav $nav): Nav
    {
        return $nav->addItem(LocationNavItem::make());
    }

    public function dashboard(Dashboard $dashboard): Dashboard
    {
        return $dashboard->addItem(DashboardItem::make()
            ->icon('map-marker-alt')
            ->label(Yii::t('location', 'Create New Location'))
            ->roles([Location::AUTH_LOCATION_CREATE])
            ->url(['/admin/location/location/create']));
    }
}
