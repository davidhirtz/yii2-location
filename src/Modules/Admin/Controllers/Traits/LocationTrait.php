<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Controllers\Traits;

use Hirtz\Location\Models\Location;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

trait LocationTrait
{
    protected function findLocation(int $id, ?string $permissionName = null): Location
    {
        $location = Location::findOne($id);

        if (!$location) {
            throw new NotFoundHttpException();
        }

        if ($permissionName) {
            $this->checkLocationPermission($location, $permissionName);
        }

        return $location;
    }

    protected function checkLocationPermission($location, $permissionName): void
    {
        if (!$this->webuser->can($permissionName, ['location' => $location])) {
            throw new ForbiddenHttpException();
        }
    }
}
