<?php
/**
 * @see \Hirtz\Location\Modules\Admin\Controllers\TagController::actionIndex()
 *
 * @var View $this
 * @var TagActiveDataProvider $provider
 */

use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Location\Modules\Admin\Widgets\Grids\TagGridView;
use Hirtz\Location\Modules\Admin\Widgets\Navs\Submenu;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Bootstrap\Panel;

$this->title(Yii::t('location', 'Tags'));
?>

<?= Submenu::widget(); ?>

<?= Panel::widget([
    'content' => TagGridView::widget([
        'dataProvider' => $provider,
    ]),
]); ?>