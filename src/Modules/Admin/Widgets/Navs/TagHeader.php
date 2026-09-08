<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Skeleton\I18n\Lang;
use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Navs\Header;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Hirtz\Skeleton\Widgets\Traits\ProviderTrait;
use Override;
use Stringable;

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
        $this->title ??= $this->model?->getOldAttribute('name') ?? Lang::t('location', 'COMMON_TAGS');

        if ($this->model) {
            $this->addContent($this->getTagActionDropdown());
        }

        if ($this->provider) {
            $this->subtitle ??= $this->getPaginationSubtitle($this->provider);
            $this->addContent($this->getCreateTagButton());
        }

        $this->addLocationBreadcrumb();

        if (!$this->provider) {
            $this->addTagBreadcrumb();
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
            ->label(Lang::t('location', 'TAG_HEADER_NEW_TAG'))
            ->roles([Tag::AUTH_TAG_CREATE]);
    }

    protected function addLocationBreadcrumb(): void
    {
        $this->addBreadcrumb(Lang::t('location', 'COMMON_LOCATIONS'), ['/admin/location/']);
    }

    protected function addTagBreadcrumb(): void
    {
        $this->addBreadcrumb(Lang::t('location', 'COMMON_TAGS'), ['/admin/location/tag/']);
    }
}
