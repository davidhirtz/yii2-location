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
use Hirtz\Location\Modules\Admin\Widgets\Navs\TagHeader;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Forms\FormContainer;

echo TagHeader::make()
    ->title(Yii::t('location', 'TAG_CREATE_TITLE'));

echo FormContainer::make()
    ->form(TagActiveForm::make()
        ->model($tag));
