<?php
/**
 * @see \davidhirtz\yii2\location\modules\admin\controllers\LocationController::actionIndex()
 *
 * @var View $this
 * @var LocationActiveDataProvider $provider
 */

use davidhirtz\yii2\location\modules\admin\data\LocationActiveDataProvider;
use davidhirtz\yii2\location\modules\admin\widgets\grids\LocationGridView;
use davidhirtz\yii2\location\modules\admin\widgets\navs\Submenu;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\bootstrap\Panel;

$this->setTitle(Yii::t('location', 'Locations'));
?>

<?= Submenu::widget(); ?>

<?= Panel::widget([
    'content' => LocationGridView::widget([
        'dataProvider' => $provider,
    ]),
]); ?>