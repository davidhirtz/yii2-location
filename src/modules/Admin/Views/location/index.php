<?php
/**
 * @see \Hirtz\Location\Modules\Admin\Controllers\LocationController::actionIndex()
 *
 * @var View $this
 * @var LocationActiveDataProvider $provider
 */

use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Hirtz\Location\Modules\Admin\Widgets\Grids\LocationGridView;
use Hirtz\Location\Modules\Admin\Widgets\Navs\Submenu;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Bootstrap\Panel;

$this->title(Yii::t('location', 'Locations'));
?>

<?= Submenu::widget(); ?>

<?= Panel::widget([
    'content' => LocationGridView::widget([
        'dataProvider' => $provider,
    ]),
]); ?>