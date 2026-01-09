<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Module;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Widgets\Navs\NavItem;
use Hirtz\Skeleton\Widgets\Navs\Submenu;
use Yii;

class LocationSubmenu extends Submenu
{
    use ModuleTrait;

    protected ?Location $location = null;
    private Module $parentModule;

    public function location(Location $location): static
    {
        $this->location = $location;
        return $this;
    }

    protected function configure(): void
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('admin')->getModule('location');
        $this->parentModule = $module;

        $this->items = $this->location ? $this->getLocationItems() : $this->getLocationGridViewItems();

        $this->title ??= $this->parentModule->getName();
        $this->url ??= $this->parentModule->url;

        $this->setBreadcrumbs();

        parent::configure();
    }

    protected function getLocationGridViewItems(): array
    {
        return [
            NavItem::make()
                ->label(Yii::t('location', 'Locations'))
                ->url(['location/index'])
                ->visible($this->webuser->can(Location::AUTH_LOCATION_CREATE))
                ->routes(['location/'])
                ->icon('map-marker-alt'),
            NavItem::make()
                ->label(Yii::t('location', 'Tags'))
                ->url(['tag/index'])
                ->visible(static::getModule()->enableTags && $this->webuser->can(Tag::AUTH_TAG_CREATE))
                ->routes(['tag/'])
                ->icon('tags'),
        ];
    }

    protected function getLocationItems(): array
    {
        return [
            NavItem::make()
                ->label(Yii::t('location', 'Location'))
                ->url($this->location->getAdminRoute())
                ->routes(['location/'])
                ->icon('map-marker-alt'),
            NavItem::make()
                ->label(Yii::t('location', 'Tags'))
                ->url(['location-tag/index', 'location' => $this->location->id])
                ->visible(static::getModule()->enableTags)
                ->badge($this->location->tag_count ?: null)
                ->routes(['location-tag/'])
                ->icon('tags'),
        ];
    }

    protected function setBreadcrumbs(): void
    {
        $this->view->addBreadcrumb($this->parentModule->getName(), ['/admin/location/index']);
    }
}
