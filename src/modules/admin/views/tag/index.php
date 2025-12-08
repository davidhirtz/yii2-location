<?php
/**
 * @see \Hirtz\Location\modules\admin\controllers\TagController::actionIndex()
 *
 * @var View $this
 * @var TagActiveDataProvider $provider
 */

use Hirtz\Location\modules\admin\data\TagActiveDataProvider;
use Hirtz\Location\modules\admin\widgets\grids\TagGridView;
use Hirtz\Location\modules\admin\widgets\navs\Submenu;
use Hirtz\Skeleton\web\View;
use Hirtz\Skeleton\widgets\bootstrap\Panel;

$this->title(Yii::t('location', 'Tags'));
?>

<?= Submenu::widget(); ?>

<?= Panel::widget([
    'content' => TagGridView::widget([
        'dataProvider' => $provider,
    ]),
]); ?>