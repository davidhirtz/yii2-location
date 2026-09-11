<?php

declare(strict_types=1);

/**
 * @see LocationController::actionCreate()
 *
 * @var View $this
 * @var Location $location
 */

use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Location\Modules\Admin\Widgets\Forms\LocationActiveForm;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationHeader;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Forms\FormContainer;

echo LocationHeader::make()
    ->title(Yii::t('location', 'LOCATION_CREATE_TITLE'));

echo FormContainer::make()
    ->form(LocationActiveForm::make()
        ->model($location));
