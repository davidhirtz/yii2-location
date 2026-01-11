<?php

declare(strict_types=1);

/**
 * @see TagController::actionCreate()
 *
 * @var View $this
 * @var Tag $tag
 */

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Location\Modules\Admin\Widgets\Forms\TagActiveForm;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationSubmenu;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Forms\FormContainer;

$this->title(Yii::t('location', 'Create New Tag'));

echo LocationSubmenu::make();

echo FormContainer::make()
    ->title($this->title)
    ->form(TagActiveForm::make()
        ->model($tag));
