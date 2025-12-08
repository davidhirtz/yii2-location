<?php
/**
 * @see \Hirtz\Location\Modules\Admin\Controllers\LocationTagController::actionIndex()
 *
 * @var View $this
 * @var TagActiveDataProvider $provider
 */

use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Location\Modules\Admin\Widgets\Grids\LocationTagGridView;
use Hirtz\Location\Modules\Admin\Widgets\Navs\Submenu;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Bootstrap\Panel;

$this->title(Yii::t('location', 'Tags'));
?>

<?= Submenu::widget([
    'location' => $provider->location,
]); ?>

<?= Panel::widget([
    'content' => LocationTagGridView::widget([
        'dataProvider' => $provider,
    ]),
]); ?>