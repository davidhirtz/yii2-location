<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Buttons;

use Hirtz\Skeleton\Widgets\Buttons\DeleteButton;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Override;
use Yii;

/**
 * @see LocationController::actionDelete()
 */
class LocationDeleteButton extends DeleteButton
{
    /**
     * @use ModelTrait<Location>
     */
    use ModelTrait;

    public function isVisible(): bool
    {
        return parent::isVisible()
            && $this->webuser->can(Location::AUTH_LOCATION_DELETE, ['location' => $this->model]);
    }

    #[Override]
    protected function configure(): void
    {
        $this->label ??= Yii::t('location', 'Delete Location');

        parent::configure();
    }
}
