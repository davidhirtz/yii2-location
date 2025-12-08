<?php
/**
 * @see LocationController::actionCreate()
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

$this->title(Yii::t('location', 'Create New Location'));
?>

<?= Submenu::widget(); ?>

<?= Html::errorSummary($location); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => LocationActiveForm::widget([
        'model' => $location,
    ]),
]); ?>
