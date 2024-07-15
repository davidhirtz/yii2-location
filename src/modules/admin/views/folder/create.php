<?php
/**
 * @see FolderController::actionCreate()
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

$this->setTitle(Yii::t('location', 'Create New Folder'));
?>

<?= Submenu::widget(); ?>

<?= Html::errorSummary($folder); ?>

<?= Panel::widget([
    'title' => $this->title,
    'content' => LocationActiveForm::widget([
        'model' => $folder,
    ]),
]); ?>
