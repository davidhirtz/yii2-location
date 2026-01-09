<?php
/**
 * @see \Hirtz\Location\Modules\Admin\Controllers\LocationController::actionIndex()
 *
 * @var View $this
 * @var LocationActiveDataProvider $provider
 */

use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Hirtz\Location\Modules\Admin\Widgets\Grids\LocationGridView;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationSubmenu;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Grids\GridContainer;

$this->title(Yii::t('location', 'Locations'));

echo LocationSubmenu::make();

echo GridContainer::make()
    ->grid(LocationGridView::make()
    ->provider($provider));
