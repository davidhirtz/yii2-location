<?php
/**
 * @see \Hirtz\Location\modules\admin\controllers\LocationTagController::actionIndex()
 *
 * @var View $this
 * @var TagActiveDataProvider $provider
 */

use Hirtz\Location\modules\admin\data\TagActiveDataProvider;
use Hirtz\Location\modules\admin\widgets\grids\LocationTagGridView;
use Hirtz\Location\modules\admin\widgets\navs\Submenu;
use Hirtz\Skeleton\web\View;
use Hirtz\Skeleton\widgets\bootstrap\Panel;

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