<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Grids;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Html\A;
use Hirtz\Skeleton\Html\Div;
use Hirtz\Skeleton\Widgets\Buttons\CreateButton;
use Hirtz\Skeleton\Widgets\Grids\Columns\BadgeColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\ButtonColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\Buttons\ViewGridButton;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Hirtz\Skeleton\Widgets\Grids\Columns\DataColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\RelativeTimeColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\StatusIconColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\TypeColumn;
use Hirtz\Skeleton\Widgets\Grids\GridView;
use Hirtz\Skeleton\Widgets\Grids\Toolbars\StatusFilterDropdown;
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

    #[Override]
    protected function configure(): void
    {
        $this->attributes['id'] ??= 'tag-grid-view';

        $this->header ??= [
            $this->getStatusDropdown(),
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

        parent::configure();
    }

    protected function getStatusDropdown(): ?Stringable
    {
        return StatusFilterDropdown::make()
            ->model(Tag::instance());
    }

    protected function getStatusColumn(): ?Column
    {
        return StatusIconColumn::make()
            ->enableUpdate($this->enableStatusUpdate
                && !$this->isPicker()
                && $this->webuser->can(Tag::AUTH_TAG));
    }

    protected function getTypeColumn(): ?Column
    {
        return TypeColumn::make()
            ->url($this->getRecordUrl(...))
            ->visible($this->hasVisibleTypes());
    }

    protected function hasVisibleTypes(): bool
    {
        return count(Tag::instance()::getTypeDefinitions()) > 1;
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
        $url = $this->getRecordUrl($tag);

        return $url
            ? A::make()->content($content)->href($url)->class('strong')
            : Div::make()->content($content)->class('strong');
    }

    /**
     * Whether the grid is a list to pick a tag *from* rather than to navigate. A picker must not lead away from
     * itself — that cancels the flow the user is in — so its name and type icon carry no link, its location count
     * badge carries none either, and the tag's own page is an external link button.
     */
    protected function isPicker(): bool
    {
        return false;
    }

    /**
     * Where the row's own links lead: the name and the type icon.
     *
     * @return array<array-key, mixed>|null
     */
    protected function getRecordUrl(Tag $tag): ?array
    {
        return $this->isPicker() ? null : ($tag->getAdminRoute() ?: null);
    }

    protected function getLocationCountColumn(): ?Column
    {
        return BadgeColumn::make()
            ->property('location_count')
            ->url($this->isPicker()
                ? null
                : fn (Tag $tag): array => ['/admin/location/location/index', 'tag' => $tag->id]);
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

    /**
     * @return list<Stringable>
     */
    protected function getButtonColumnContent(Tag $tag): array
    {
        return [
            ViewGridButton::make()
                ->model($tag),
        ];
    }
}
