<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Widgets\Navs\NavItem;
use Override;
use Yii;

class LocationNavItem extends NavItem
{
    use ModuleTrait;

    protected bool $showTags = true;

    public function __construct(array $config = [])
    {
        $this->label ??= Yii::t('location', 'Places');
        $this->icon ??= 'map-marked-alt';
        $this->order ??= 50;
        $this->url ??= ['/admin/location/location/index'];

        parent::__construct($config);
    }

    #[Override]
    protected function configure(): void
    {
        if ($this->showTags) {
            $this->showTags = static::getModule()->enableTags;
        }

        $this->addSubnavItems();
        parent::configure();
    }

    protected function addSubnavItems(): void
    {
        $this->addItem($this->getLocationIndexItem(), $this->getTagIndexItem());
    }

    protected function getLocationIndexItem(): ?NavItem
    {
        return NavItem::make()
            ->icon('map-marker-alt')
            ->label(Yii::t('location', 'Locations'))
            ->order(10)
            ->url(['/admin/location/location/index'])
            ->roles([Location::AUTH_LOCATION_CREATE])
            ->routes(['admin/location/location/', 'admin/location/location-tag/']);
    }

    protected function getTagIndexItem(): ?NavItem
    {
        return $this->showTags
            ? NavItem::make()
                ->icon('tags')
                ->label(Yii::t('location', 'Tags'))
                ->order(20)
                ->url(['/admin/location/tag/index'])
                ->roles([Tag::AUTH_TAG_CREATE])
                ->routes(['admin/location/tag'])
            : null;
    }
}
