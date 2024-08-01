<?php

namespace davidhirtz\yii2\location\modules\admin\controllers\traits;

use davidhirtz\yii2\location\models\Location;
use Yii;
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
        if (!Yii::$app->getUser()->can($permissionName, ['location' => $location])) {
            throw new ForbiddenHttpException();
        }
    }
}
