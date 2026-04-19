<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Grids;

use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Html\A;
use Hirtz\Skeleton\Html\Div;
use Hirtz\Skeleton\Widgets\Buttons\Button;
use Hirtz\Skeleton\Widgets\Buttons\ButtonGroup;
use Hirtz\Skeleton\Widgets\Grids\Columns\BadgeColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\ButtonColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\Buttons\ViewGridButton;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Hirtz\Skeleton\Widgets\Grids\Columns\DataColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\RelativeTimeColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\StatusIconColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\TypeColumn;
use Hirtz\Skeleton\Widgets\Grids\GridView;
use Hirtz\Skeleton\Widgets\Grids\Toolbars\FilterDropdown;
use Hirtz\Skeleton\Widgets\Grids\Toolbars\StatusFilterDropdown;
use Hirtz\Skeleton\Widgets\Grids\Toolbars\TypeFilterDropdown;
use Override;
use Stringable;
use Yii;

/**
 * @extends GridView<Location>
 * @property LocationActiveDataProvider $dataProvider
 */
class LocationGridView extends GridView
{
    use ModuleTrait;

    protected bool $showTagDropdown = true;
    protected bool $showTags = true;
    protected bool $showTypeDropdown = true;

    #[Override]
    protected function configure(): void
    {
        $this->showTags = $this->showTags && static::getModule()->enableTags;

        if ($this->showTagDropdown) {
            $this->showTagDropdown = static::getModule()->enableTags && count(TagCollection::getAll()) !== 0;
        }

        if ($this->showTypeDropdown) {
            $this->showTypeDropdown = count(Location::getTypes()) > 1;
        }

        $this->header ??= [
            $this->getStatusDropdown(),
            $this->getTypeDropdown(),
            $this->getTagDropdown(),
            $this->getSearchInput(),
        ];

        $this->columns ??= [
            $this->getStatusColumn(),
            $this->getTypeColumn(),
            $this->getNameColumn(),
            $this->getTagCountColumn(),
            $this->getUpdatedAtColumn(),
            $this->getButtonColumn(),
        ];

        parent::configure();
    }

    protected function getStatusDropdown(): ?Stringable
    {
        return StatusFilterDropdown::make()
            ->model(Location::instance());
    }

    protected function getTypeDropdown(): ?Stringable
    {
        return TypeFilterDropdown::make()
            ->model(Location::instance());
    }

    protected function getTagDropdown(): ?FilterDropdown
    {
        return FilterDropdown::make()
            ->label(Yii::t('skeleton', 'Tags'))
            ->items($this->getTagDropdownItems())
            ->visible($this->showTagDropdown)
            ->paramName('tag');
    }

    protected function getTagDropdownItems(): array
    {
        return array_map(fn (Tag $tag) => $tag->getI18nAttribute('name'), TagCollection::getAll());
    }

    protected function getStatusColumn(): ?Column
    {
        return StatusIconColumn::make();
    }

    protected function getTypeColumn(): ?Column
    {
        return TypeColumn::make()
            ->url(fn (Location $location) => $location->getAdminRoute())
            ->visible($this->showTypeDropdown);
    }

    protected function getNameColumn(): ?Column
    {
        return DataColumn::make()
            ->property('name')
            ->content($this->getNameColumnContent(...));
    }

    protected function getNameColumnContent(Location $location): string
    {
        if ($address = $location->formatted_address) {
            $address = $this->search->markKeywords($address);
        }

        if ($name = $location->getI18nAttribute('name')) {
            $name = $this->search->markKeywords($name);

            $content = A::make()
                ->class('strong')
                ->content($name)
                ->href($location->getAdminRoute());

            if ($address) {
                $content .= Div::make()
                    ->content($address)
                    ->addClass('small');
            }
        } else {
            $name = $address ?: Yii::t('location', 'Unnamed');

            $content = A::make()
                ->class('strong')
                ->content($name)
                ->href($location->getAdminRoute());
        }

        if ($this->showTags) {
            $content .= $this->getTagButtons($location);
        }

        return $content;
    }

    protected function getTagCountColumn(): ?Column
    {
        return BadgeColumn::make()
            ->property('tag_count')
            ->visible(static::getModule()->enableTags)
            ->url(fn (Location $location) => ['/admin/location/location-tag/index', 'location' => $location->id]);
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

    protected function getButtonColumnContent(Location $location): array
    {
        return [
            ViewGridButton::make()
                ->model($location),
        ];
    }

    protected function getTagButtons(Location $location): ?Stringable
    {
        $tags = [];

        foreach (TagCollection::getByLocation($location) as $tag) {
            $tags[] = Button::make()
                ->secondary()
                ->text($tag->getI18nAttribute('name'))
                ->current(['tag' => $tag->id, 'page' => null])
                ->addClass('btn-sm');
        }

        return $tags ? ButtonGroup::make()->content(...$tags) : null;
    }
}
