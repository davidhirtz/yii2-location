<?php
/**
 * @see LocationController::actionUpdate()
 * @see LocationController::actionDelete()
 *
 * @var View $this
 * @var Location $location
 */

use Hirtz\Location\models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Location\Modules\Admin\Widgets\Forms\LocationActiveForm;
use Hirtz\Location\Modules\Admin\Widgets\Navs\Submenu;
use Hirtz\Skeleton\Helpers\Html;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Bootstrap\Panel;
use Hirtz\Skeleton\Widgets\Forms\DeleteActiveForm;

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