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

        if (!$location) {
            throw new NotFoundHttpException();
        }

        return $location;
    }
}
