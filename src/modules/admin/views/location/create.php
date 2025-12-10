<?php
/**
 * @see LocationController::actionCreate()
 *
 * @var View $this
 * @var Location $location
 */

use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Location\Modules\Admin\Widgets\Forms\LocationActiveForm;
use Hirtz\Location\Modules\Admin\Widgets\Navs\Submenu;
use Hirtz\Skeleton\Helpers\Html;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Bootstrap\Panel;

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
