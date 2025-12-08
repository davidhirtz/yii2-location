<?php
/**
 * @see TagController::actionUpdate()
 * @see TagController::actionDelete()
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
use Hirtz\Skeleton\widgets\forms\DeleteActiveForm;

$this->title(Yii::t('location', 'Edit Tag'));
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