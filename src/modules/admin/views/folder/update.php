<?php
/**
 * @see FolderController::actionUpdate()
 *
 * @var View $this
 * @var Folder $folder
 */

use davidhirtz\yii2\location\models\Folder;
use davidhirtz\yii2\location\modules\admin\controllers\FolderController;
use davidhirtz\yii2\location\modules\admin\widgets\forms\LocationActiveForm;
use davidhirtz\yii2\location\modules\admin\widgets\navs\Submenu;
use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\bootstrap\Panel;
use davidhirtz\yii2\skeleton\widgets\forms\DeleteActiveForm;

$this->setTitle(Yii::t('location', 'Edit Folder'));
?>

<?= Submenu::widget(); ?>

<?= Html::errorSummary($folder); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => LocationActiveForm::widget([
        'model' => $folder,
    ]),
]); ?>

<?php if ($folder->isDeletable()) {
    echo Panel::widget([
        'type' => 'danger',
        'title' => Yii::t('location', 'Delete Folder'),
        'content' => DeleteActiveForm::widget([
            'model' => $folder,
            'attribute' => 'name',
            'message' => Yii::t('location', 'Please type the folder name in the text field below to delete all related files. This cannot be undone, please be certain!')
        ]),
    ]);
} ?>