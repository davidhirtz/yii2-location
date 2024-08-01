<?php
/**
 * @see TagController::actionCreate()
 *
 * @var View $this
 * @var Tag $tag
 */

use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\location\modules\admin\controllers\TagController;
use davidhirtz\yii2\location\modules\admin\widgets\forms\TagActiveForm;
use davidhirtz\yii2\location\modules\admin\widgets\navs\Submenu;
use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\bootstrap\Panel;

$this->setTitle(Yii::t('location', 'Create New Tag'));
?>

<?= Submenu::widget(); ?>

<?= Html::errorSummary($tag); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => TagActiveForm::widget([
        'model' => $tag,
    ]),
]); ?>
