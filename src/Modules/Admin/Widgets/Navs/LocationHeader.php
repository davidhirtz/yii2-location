<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Navs\Header;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Hirtz\Skeleton\Widgets\Traits\ProviderTrait;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Override;
use Stringable;
use Yii;

class LocationHeader extends Header
{
    /**
     * @use ModelTrait<Location|null>
     */
    use ModelTrait;

    /**
     * @use ProviderTrait<LocationActiveDataProvider|null>
     */
    use ProviderTrait;

    #[Override]
    protected function configure(): void
    {
        $this->title ??= $this->model?->getOldAttribute('name') ?? Yii::t('location', 'Locations');

        if ($this->model) {
            $this->addContent($this->getLocationActionDropdown());
        }

        if ($this->provider) {
            $this->subtitle ??= $this->getPaginationSubtitle($this->provider);
            $this->addContent($this->getCreateLocationButton());
        }

        if (!$this->provider) {
            $this->view->addBreadcrumb(Yii::t('location', 'Locations'), ['/admin/location/']);
        }

        parent::configure();
    }

    protected function getLocationActionDropdown(): ?Stringable
    {
        return LocationActionDropdown::make()
            ->model($this->model);
    }

    /**
     * @see LocationController::actionCreate()
     */
    protected function getCreateLocationButton(): string|Stringable
    {
        return CreateButton::make()
            ->label(Yii::t('location', 'New Location'))
            ->roles([Location::AUTH_LOCATION_CREATE]);
    }
}
