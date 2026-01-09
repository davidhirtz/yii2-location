<?php
/**
 * @see TagController::actionUpdate()
 * @see TagController::actionDelete()
 *
 * @var View $this
 * @var Tag $tag
 */

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Location\Modules\Admin\Widgets\Forms\TagActiveForm;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationSubmenu;
use Hirtz\Skeleton\Helpers\Html;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Bootstrap\Panel;
use Hirtz\Skeleton\Widgets\Forms\DeleteActiveForm;

$this->title(Yii::t('location', 'Edit Tag'));
?>

<?= LocationSubmenu::widget(); ?>
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