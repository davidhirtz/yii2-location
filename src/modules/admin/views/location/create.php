<?php
/**
 * @see LocationController::actionCreate()
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

$this->setTitle(Yii::t('location', 'Create New Location'));
?>

<?= Submenu::widget(); ?>

<?= Html::errorSummary($location); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => LocationActiveForm::widget([
        'model' => $location,
    ]),
]); ?>
