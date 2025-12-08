<?php
/**
 * @see TagController::actionCreate()
 *
 * @var View $this
 * @var Tag $tag
 */

use Hirtz\Location\models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Location\Modules\Admin\Widgets\Forms\TagActiveForm;
use Hirtz\Location\Modules\Admin\Widgets\Navs\Submenu;
use Hirtz\Skeleton\Helpers\Html;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Bootstrap\Panel;

$this->title(Yii::t('location', 'Create New Tag'));
?>

<?= Submenu::widget(); ?>

<?= Html::errorSummary($tag); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => TagActiveForm::widget([
        'model' => $tag,
    ]),
]); ?>
