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
    ->title(Yii::t('location', 'Create New Location'));

echo FormContainer::make()
    ->title($this->title)
    ->form(LocationActiveForm::make()
        ->model($location));
