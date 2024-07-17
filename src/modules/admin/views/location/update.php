<?php
/**
 * @see LocationController::actionUpdate()
 * @see LocationController::actionDelete()
 *
 * @var View $this
 * @var Location $location
 */

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\modules\admin\controllers\LocationController;
use davidhirtz\yii2\location\modules\admin\widgets\forms\LocationActiveForm;
use davidhirtz\yii2\location\modules\admin\widgets\navs\Submenu;
use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\bootstrap\Panel;
use davidhirtz\yii2\skeleton\widgets\forms\DeleteActiveForm;

$this->setTitle(Yii::t('location', 'Edit Location'));
?>

<?= Submenu::widget(); ?>

<?= Html::errorSummary($location); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => LocationActiveForm::widget([
        'model' => $location,
    ]),
]); ?>

<?php if (Yii::$app->getUser()->can(Location::AUTH_LOCATION_DELETE, ['location' => $location])) {
    echo Panel::widget([
        'type' => 'danger',
        'title' => Yii::t('location', 'Delete Location'),
        'content' => DeleteActiveForm::widget([
            'model' => $location,
        ]),
    ]);
} ?>