<?php
/**
 * @see LocationController::actionUpdate()
 * @see LocationController::actionDelete()
 *
 * @var View $this
 * @var Location $location
 */

use Hirtz\Location\models\Location;
use Hirtz\Location\modules\admin\controllers\LocationController;
use Hirtz\Location\modules\admin\widgets\forms\LocationActiveForm;
use Hirtz\Location\modules\admin\widgets\navs\Submenu;
use Hirtz\Skeleton\helpers\Html;
use Hirtz\Skeleton\web\View;
use Hirtz\Skeleton\widgets\bootstrap\Panel;
use Hirtz\Skeleton\widgets\forms\DeleteActiveForm;

$this->title(Yii::t('location', 'Edit Location'));
?>

<?= Submenu::widget([
    'location' => $location,
]); ?>

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