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
use Hirtz\Location\Modules\Admin\Widgets\Navs\TagHeader;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Grids\GridContainer;

echo TagHeader::make()
    ->provider($provider);

echo GridContainer::make()
    ->grid(TagGridView::make()
        ->provider($provider));
