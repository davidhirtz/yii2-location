<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Buttons;

use Hirtz\Skeleton\Widgets\Buttons\DeleteButton;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Override;
use Yii;

/**
 * @see LocationController::actionDelete()
 *
 * @extends DeleteButton<Location>
 */
class LocationDeleteButton extends DeleteButton
{
    #[\Override]
    public function isVisible(): bool
    {
        return parent::isVisible()
            && $this->webuser->can(Location::AUTH_LOCATION);
    }

    #[Override]
    protected function configure(): void
    {
        $this->label ??= Yii::t('location', 'LOCATION_DELETE_BUTTON');
        $this->title ??= Yii::t('location', 'LOCATION_DELETE_CONFIRM');

        // `LocationHeader` renders on the tags tab too, where the parent's relative `delete` would be that tab's own
        $this->url ??= ['/admin/location/location/delete', 'id' => $this->model->id];

        parent::configure();
    }
}
