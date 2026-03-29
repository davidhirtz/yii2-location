<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Grids;

use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Helpers\Html;
use Hirtz\Skeleton\Html\A;
use Hirtz\Skeleton\Html\Div;
use Hirtz\Skeleton\Widgets\Buttons\Button;
use Hirtz\Skeleton\Widgets\Grids\Columns\BadgeColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\ButtonColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\Buttons\ViewGridButton;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Hirtz\Skeleton\Widgets\Grids\Columns\DataColumn;
use Hirtz\Skeleton\Widgets\Grids\Columns\RelativeTimeColumn;
use Hirtz\Skeleton\Widgets\Grids\GridView;
use Hirtz\Skeleton\Widgets\Grids\Toolbars\CreateButton;
use Hirtz\Skeleton\Widgets\Grids\Toolbars\FilterDropdown;
use Hirtz\Skeleton\Widgets\Grids\Traits\StatusGridViewTrait;
use Hirtz\Skeleton\Widgets\Grids\Traits\TypeGridViewTrait;
use Stringable;
use Yii;

/**
 * @extends GridView<Location>
 * @property LocationActiveDataProvider $dataProvider
 */
class LocationGridView extends GridView
{
    use ModuleTrait;
    use StatusGridViewTrait;
    use TypeGridViewTrait;

    protected bool $showTagDropdown = true;
    protected bool $showTags = true;
    protected bool $showTypeDropdown = true;

    #[\Override]
    protected function configure(): void
    {
        $this->model ??= Location::instance();
        $this->showTags = $this->showTags && static::getModule()->enableTags;

        $this->showTagDropdown = $this->showTagDropdown
            && static::getModule()->enableTags && count(TagCollection::getAll()) > 1;

        $this->showTypeDropdown = $this->showTypeDropdown && count(Location::instance()::getTypes()) > 1;

        $this->header ??= [
            $this->getTypeDropdown(),
            $this->showTagDropdown ? $this->getTagDropdown() : null,
            $this->search->getToolbarItem(),
        ];

        $this->columns ??= [
            $this->getStatusColumn(),
            $this->getTypeColumn(),
            $this->getNameColumn(),
            $this->getTagCountColumn(),
            $this->getUpdatedAtColumn(),
            $this->getButtonColumn(),
        ];

        $this->footer ??= [
            $this->getCreateLocationButton(),
        ];

        parent::configure();
    }

    protected function getTagDropdown(): ?FilterDropdown
    {
        return FilterDropdown::make()
            ->label(Yii::t('skeleton', 'Tags'))
            ->items($this->getTagDropdownItems())
            ->param('tag');
    }

    protected function getTagDropdownItems(): array
    {
        return array_map(fn (Tag $tag) => $tag->getI18nAttribute('name'), TagCollection::getAll());
    }

    protected function getCreateLocationButton(): ?Stringable
    {
        return $this->webuser->can(Location::AUTH_LOCATION_CREATE)
            ? CreateButton::make()
                ->text(Yii::t('location', 'New Location'))
                ->href(['/admin/location/create'])
            : null;
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
            $address = Html::markKeywords(Html::encode($address), $this->search->getKeywords());
        }

        if ($name = $location->getI18nAttribute('name')) {
            $name = Html::markKeywords(Html::encode($name), $this->search->getKeywords());

            $content = A::make()
                ->content($name)
                ->href($location->getAdminRoute())
                ->addClass('strong');

            if ($address) {
                $content .= Div::make()
                    ->content($address)
                    ->addClass('small');
            }
        } else {
            $name = $address ?: Yii::t('location', 'Unnamed');

            $content = A::make()
                ->content($name)
                ->href($location->getAdminRoute())
                ->addClass('strong');
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
            ->url(fn (Location $location) => ['/admin/location-tag/index', 'location' => $location->id]);
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

        return $tags
            ? Div::make()
                ->class('btn-group')
                ->content(...$tags)
            : null;
    }
}
