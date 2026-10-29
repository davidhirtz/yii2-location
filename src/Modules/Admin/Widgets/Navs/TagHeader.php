<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Navs\ModelHeader;
use Hirtz\Skeleton\Widgets\Traits\ProviderTrait;
use Override;
use Stringable;
use Yii;

/**
 * @extends ModelHeader<Tag|null>
 */
class TagHeader extends ModelHeader
{
    /**
     * @use ProviderTrait<TagActiveDataProvider|null>
     */
    use ProviderTrait;

    #[Override]
    protected function configure(): void
    {
        if ($this->model) {
            $this->title ??= $this->model->getOldAttribute('name');
            $this->addContent($this->getTagActionDropdown());
        }

        if ($this->provider) {
            $this->subtitle ??= $this->getPaginationSubtitle($this->provider);
            $this->addContent($this->getCreateTagButton());
        }

        $this->title ??= Yii::t('location', 'TAG_PLURAL');

        $this->addLocationBreadcrumb();

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
            ->label(Yii::t('location', 'TAG_CREATE_BUTTON'))
            ->roles([Tag::AUTH_TAG]);
    }

    protected function addLocationBreadcrumb(): void
    {
        if (!$this->webuser->can(Location::AUTH_LOCATION)) {
            return;
        }

        $this->addBreadcrumb(Yii::t('location', 'LOCATION_PLURAL'), ['/admin/location/']);
    }
}
