<?php
/**
 * @see TagController::actionCreate()
 *
 * @var View $this
 * @var Tag $tag
 */

use Hirtz\Location\models\Tag;
use Hirtz\Location\modules\admin\controllers\TagController;
use Hirtz\Location\modules\admin\widgets\forms\TagActiveForm;
use Hirtz\Location\modules\admin\widgets\navs\Submenu;
use Hirtz\Skeleton\helpers\Html;
use Hirtz\Skeleton\web\View;
use Hirtz\Skeleton\widgets\bootstrap\Panel;

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
