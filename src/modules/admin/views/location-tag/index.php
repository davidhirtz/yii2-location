<?php
/**
 * @see \davidhirtz\yii2\location\modules\admin\controllers\LocationTagController::actionIndex()
 *
 * @var View $this
 * @var TagActiveDataProvider $provider
 */

use davidhirtz\yii2\location\modules\admin\data\TagActiveDataProvider;
use davidhirtz\yii2\location\modules\admin\widgets\grids\LocationTagGridView;
use davidhirtz\yii2\location\modules\admin\widgets\grids\TagGridView;
use davidhirtz\yii2\location\modules\admin\widgets\navs\Submenu;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\bootstrap\Panel;

$this->setTitle(Yii::t('location', 'Tags'));
?>

<?= Submenu::widget([
    'location' => $provider->location,
]); ?>

<?= Panel::widget([
    'content' => LocationTagGridView::widget([
        'dataProvider' => $provider,
    ]),
]); ?>