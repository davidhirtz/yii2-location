<?php
/**
 * Folders.
 * @see \davidhirtz\yii2\location\modules\admin\controllers\FolderController::actionIndex()
 *
 * @var \davidhirtz\yii2\skeleton\web\View $this
 * @var \yii\data\ActiveDataProvider $provider
 * @var \davidhirtz\yii2\location\models\Folder $folder
 */

use davidhirtz\yii2\location\modules\admin\widgets\grids\LocationGridView;
use davidhirtz\yii2\location\modules\admin\widgets\navs\Submenu;
use davidhirtz\yii2\skeleton\widgets\bootstrap\Panel;

$this->setTitle(Yii::t('location', 'Folders'));
?>

<?= Submenu::widget(); ?>

<?= Panel::widget([
    'content' => LocationGridView::widget([
        'dataProvider' => $provider,
        'folder' => $folder,
    ]),
]); ?>