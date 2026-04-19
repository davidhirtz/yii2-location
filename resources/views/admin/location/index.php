<?php

declare(strict_types=1);

/**
 * @see \Hirtz\Location\Modules\Admin\Controllers\LocationController::actionIndex()
 *
 * @var View $this
 * @var LocationActiveDataProvider $provider
 */

use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Hirtz\Location\Modules\Admin\Widgets\Grids\LocationGridView;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationHeader;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Grids\GridContainer;

echo LocationHeader::make()
    ->provider($provider);

echo GridContainer::make()
    ->grid(LocationGridView::make()
    ->provider($provider));
