<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Controllers\Traits;

use Hirtz\Location\Models\Location;
use yii\web\NotFoundHttpException;

trait LocationTrait
{
    protected function findLocation(int $id): Location
    {
        $location = Location::findOne($id);

        if (!$location || !$this->isLocationAllowed($location)) {
            throw new NotFoundHttpException();
        }

        return $location;
    }

    /**
     * Whether the controller works with this location at all. A controller behind a submenu tab answers with the
     * capability that tab is shown for, so a route cannot do what the admin does not offer.
     */
    protected function isLocationAllowed(Location $location): bool
    {
        return true;
    }
}
