<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Grids;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Html\A;
use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Grids\Columns\BadgeColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\ButtonColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\Buttons\ViewGridButton;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Hirtz\Skeleton\Widgets\Grids\Columns\DataColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\RelativeTimeColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\StatusIconColumn;
use Hirtz\Skeleton\Widgets\Grids\GridView;
use Hirtz\Skeleton\Widgets\Grids\Toolbars\StatusFilterDropdown;

use Hirtz\Skeleton\Widgets\Grids\Traits\TypeGridViewTrait;
use Override;
use Stringable;
use Yii;

/**
 * @extends GridView<Tag>
 * @property TagActiveDataProvider $dataProvider
 */
class TagGridView extends GridView
{
    use ModuleTrait;
    use TypeGridViewTrait;

    #[Override]
    protected function configure(): void
    {
        $this->header ??= [
            $this->getStatusDropdown(),
            $this->getTypeDropdown(),
            $this->getSearchInput(),
        ];

        $this->columns ??= [
            $this->getStatusColumn(),
            $this->getTypeColumn(),
            $this->getNameColumn(),
            $this->getLocationCountColumn(),
            $this->getUpdatedAtColumn(),
            $this->getButtonColumn(),
        ];

        $this->footer ??= [
            $this->getCreateTagButton(),
        ];

        parent::configure();
    }

    protected function getStatusDropdown(): ?Stringable
    {
        return StatusFilterDropdown::make()
            ->model(Tag::instance());
    }

    protected function getCreateTagButton(): string|Stringable
    {
        return CreateButton::make()
            ->label(Yii::t('location', 'New Tag'))
            ->roles([Tag::AUTH_TAG_CREATE]);
    }

    protected function getStatusColumn(): ?Column
    {
        return StatusIconColumn::make();
    }

    protected function getNameColumn(): ?Column
    {
        return DataColumn::make()
            ->property('name')
            ->content($this->getNameColumnContent(...));
    }

    protected function getNameColumnContent(Tag $tag): ?Stringable
    {
        $content = $this->search->markKeywords($tag->getI18nAttribute('name'));

        return A::make()
            ->content($content)
            ->href($tag->getAdminRoute())
            ->class('strong');
    }

    protected function getLocationCountColumn(): ?Column
    {
        return BadgeColumn::make()
            ->property('location_count')
            ->url(fn (Tag $tag) => $tag->getAdminRoute());
    }

    protected function getUpdatedAtColumn(): ?Column
    {
        return RelativeTimeColumn::make()
            ->property('updated_at');
    }

    protected function getButtonColumn(): ?Column
    {
        return ButtonColumn::make()
            ->content($this->getButtonColumnContent(...));
    }

    protected function getButtonColumnContent(Tag $tag): array
    {
        return [
            ViewGridButton::make()
                ->model($tag),
        ];
    }
}
