<?php
/**
 * @see \Hirtz\Location\modules\admin\controllers\LocationController::actionIndex()
 *
 * @var View $this
 * @var LocationActiveDataProvider $provider
 */

use Hirtz\Location\modules\admin\data\LocationActiveDataProvider;
use Hirtz\Location\modules\admin\widgets\grids\LocationGridView;
use Hirtz\Location\modules\admin\widgets\navs\Submenu;
use Hirtz\Skeleton\web\View;
use Hirtz\Skeleton\widgets\bootstrap\Panel;

$this->title(Yii::t('location', 'Locations'));
?>

<?= Submenu::widget(); ?>

<?= Panel::widget([
    'content' => LocationGridView::widget([
        'dataProvider' => $provider,
    ]),
]); ?>