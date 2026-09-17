<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Navs\ModelHeader;
use Hirtz\Skeleton\Widgets\Traits\ProviderTrait;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Override;
use Stringable;
use Yii;

/**
 * @extends ModelHeader<Location|null>
 */
class LocationHeader extends ModelHeader
{
    /**
     * @use ProviderTrait<LocationActiveDataProvider|null>
     */
    use ProviderTrait;

    #[Override]
    protected function configure(): void
    {
        if ($this->model) {
            $this->title ??= $this->model->getOldAttribute('name');
            $this->addContent($this->getLocationActionDropdown());
        }

        if ($this->provider) {
            $this->subtitle ??= $this->getPaginationSubtitle($this->provider);
            $this->addContent($this->getCreateLocationButton());
        }

        $this->title ??= Yii::t('location', 'COMMON_LOCATIONS');

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
            ->label(Yii::t('location', 'LOCATION_HEADER_NEW_LOCATION'))
            ->roles([Location::AUTH_LOCATION]);
    }
}
