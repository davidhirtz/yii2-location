<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Grids;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Skeleton\Web\Application;
use Hirtz\Skeleton\Widgets\Buttons\Button;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Hirtz\Skeleton\Widgets\Grids\Columns\RelativeTimeColumn;
use Override;
use Stringable;
use Yii;

/**
 * @property TagActiveDataProvider $provider
 */
class LocationTagGridView extends TagGridView
{
    #[Override]
    protected function configure(): void
    {
        $this->rowAttributes ??= fn (Tag $tag) => [
            'class' => $tag->locationTag ? 'is-selected' : null,
        ];

        $this->columns ??= [
            $this->getStatusColumn(),
            $this->getTypeColumn(),
            $this->getNameColumn(),
            $this->getLocationCountColumn(),
            $this->getUpdatedAtColumn(),
            $this->getButtonColumn(),
        ];

        $this->footer = [];

        parent::configure();
    }

    #[Override]
    protected function getUpdatedAtColumn(): ?Column
    {
        $visible = array_reduce(
            $this->provider->getModels(),
            fn (bool $carry, Tag $tag) => null !== $tag->locationTag || $carry,
            false
        );

        return RelativeTimeColumn::make()
            ->title(Yii::t('location', 'LOCATION_TAG_ADDED'))
            ->visible($visible)
            ->value(fn (Tag $tag) => $tag->locationTag?->updated_at)
            ->hiddenForMediumDevices();
    }

    #[Override]
    protected function isPicker(): bool
    {
        return true;
    }

    /**
     * @return list<Stringable>
     */
    #[Override]
    protected function getButtonColumnContent(Tag $tag): array
    {
        return [
            $this->getAdminLinkButton($tag),
            Button::make()
                ->primary()
                ->icon($tag->locationTag ? 'ban' : 'star')
                ->post([
                    ...Application::current()->getRequest()->getQueryParams(),
                    $tag->locationTag ? 'delete' : 'create',
                    'location' => $this->provider->location->id,
                    'tag' => $tag->id,
                ]),
        ];
    }

    /**
     * The tag's own page, which the name no longer leads to — in a new tab, so the picker survives the detour.
     */
    protected function getAdminLinkButton(Tag $tag): Stringable
    {
        return Button::make()
            ->secondary()
            ->icon('external-link-alt')
            ->tooltip(Yii::t('location', 'COMMON_OPEN_ADMIN'))
            ->url($tag->getAdminRoute() ?: null)
            ->target('_blank');
    }
}
