<?php
/**
 * @see TagController::actionUpdate()
 * @see TagController::actionDelete()
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
use davidhirtz\yii2\skeleton\widgets\forms\DeleteActiveForm;

$this->setTitle(Yii::t('location', 'Edit Tag'));
?>

<?= Submenu::widget(); ?>
<?= Html::errorSummary($tag); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => TagActiveForm::widget([
        'model' => $tag,
    ]),
]); ?>

<?php if (Yii::$app->getUser()->can(Tag::AUTH_TAG_DELETE, ['tag' => $tag])) {
    echo Panel::widget([
        'type' => 'danger',
        'title' => Yii::t('location', 'Delete Tag'),
        'content' => DeleteActiveForm::widget([
            'model' => $tag,
        ]),
    ]);
} ?>