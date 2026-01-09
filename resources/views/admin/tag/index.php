<?php

declare(strict_types=1);

/**
 * @see \Hirtz\Location\Modules\Admin\Controllers\TagController::actionIndex()
 *
 * @var View $this
 * @var TagActiveDataProvider $provider
 */

use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Location\Modules\Admin\Widgets\Grids\TagGridView;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationSubmenu;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Grids\GridContainer;

$this->title(Yii::t('location', 'Tags'));

echo LocationSubmenu::make();

echo GridContainer::make()
    ->grid(TagGridView::make()
        ->provider($provider));
