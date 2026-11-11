<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Navs\ActionDropdown;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Widgets\Buttons\TagDeleteButton;
use Override;
use Stringable;
use Yii;

class TagActionDropdown extends ActionDropdown
{
    /**
     * @use ModelTrait<Tag>
     */
    use ModelTrait;

    #[Override]
    protected function configure(): void
    {
        $this->addItem(
            $this->getCreateButton(),
            $this->getTagDeleteButton(),
        );

        parent::configure();
    }

    /**
     * @see TagController::actionCreate()
     */
    protected function getCreateButton(): ?Stringable
    {
        return CreateButton::make()
            ->primary()
            ->label(Yii::t('location', 'TAG_CREATE_BUTTON'))
            ->roles([Tag::AUTH_TAG])
            ->url(['/admin/location/tag/create', 'type' => $this->model->type]);
    }

    protected function getTagDeleteButton(): ?Stringable
    {
        return TagDeleteButton::make()
            ->model($this->model);
    }
}
