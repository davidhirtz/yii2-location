<?php

declare(strict_types=1);

/**
 * @see LocationController::actionUpdate()
 * @see LocationController::actionDelete()
 *
 * @var View $this
 * @var Location $location
 */

use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Location\Modules\Admin\Widgets\Forms\LocationActiveForm;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationHeader;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationSubmenu;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Forms\DeleteActiveForm;
use Hirtz\Skeleton\Widgets\Forms\FormContainer;

echo LocationHeader::make()
    ->model($location);

echo LocationSubmenu::make()
    ->model($location);

echo FormContainer::make()
    ->title(Yii::t('location', 'Edit Location'))
    ->form(LocationActiveForm::make()
        ->model($location));

if (Yii::$app->getUser()->can(Location::AUTH_LOCATION_DELETE, ['location' => $location])) {
    echo FormContainer::make()
        ->danger()
        ->title(Yii::t('location', 'Delete Location'))
        ->form(DeleteActiveForm::make()
            ->model($location));
}
