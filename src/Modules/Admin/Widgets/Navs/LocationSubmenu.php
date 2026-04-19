<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Widgets\Navs\NavItem;
use Hirtz\Skeleton\Widgets\Navs\Submenu;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Override;
use Stringable;
use Yii;

class LocationSubmenu extends Submenu
{
    /**
     * @use ModelTrait<Location>
     */
    use ModelTrait;
    use ModuleTrait;


    #[Override]
    protected function configure(): void
    {
        $this->title ??= Yii::t('location', 'Locations');
        $this->url ??= ['/admin/location/location/index'];

        $this->addItem($this->getLocationItem(), $this->getLocationTagsItem());

        parent::configure();
    }

    protected function getLocationItem(): ?Stringable
    {
        return NavItem::make()
            ->label(Yii::t('location', 'Location'))
            ->url($this->model->getAdminRoute())
            ->icon('map-marker-alt');
    }

    protected function getLocationTagsItem(): ?Stringable
    {
        return NavItem::make()
            ->label(Yii::t('location', 'Tags'))
            ->url(['location-tag/index', 'location' => $this->model->id])
            ->visible(static::getModule()->enableTags)
            ->badge($this->model->tag_count ?: null)
            ->routes(['location-tag/'])
            ->icon('tags');
    }
}
