<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Navs\Header;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Hirtz\Skeleton\Widgets\Traits\ProviderTrait;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Override;
use Stringable;
use Yii;

class TagHeader extends Header
{
    /**
     * @use ModelTrait<Tag|null>
     */
    use ModelTrait;

    /**
     * @use ProviderTrait<TagActiveDataProvider|null>
     */
    use ProviderTrait;

    #[Override]
    protected function configure(): void
    {
        $this->title ??= $this->model?->getOldAttribute('name') ?? Yii::t('location', 'Tags');

        if ($this->model) {
            $this->addContent($this->getTagActionDropdown());
        }

        if ($this->provider) {
            $this->subtitle ??= $this->getPaginationSubtitle($this->provider);
            $this->addContent($this->getCreateTagButton());
        }

        if (!$this->provider) {
            $this->view->addBreadcrumb(Yii::t('location', 'Locations'), ['/admin/location/']);
            $this->view->addBreadcrumb(Yii::t('location', 'Tags'), ['/admin/location/tag/']);
        }

        parent::configure();
    }

    protected function getTagActionDropdown(): ?Stringable
    {
        return TagActionDropdown::make()
            ->model($this->model);
    }

    /**
     * @see TagController::actionCreate()
     */
    protected function getCreateTagButton(): string|Stringable
    {
        return CreateButton::make()
            ->label(Yii::t('location', 'New Tag'))
            ->roles([Tag::AUTH_TAG_CREATE]);
    }
}
