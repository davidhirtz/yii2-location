<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Grids;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Skeleton\Html\Button;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Hirtz\Skeleton\Widgets\Grids\Columns\RelativeTimeColumn;
use Override;
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
            ->label(Yii::t('location', 'Added'))
            ->visible($visible)
            ->value(fn (Tag $tag) => $tag->locationTag?->updated_at)
            ->hiddenForMediumDevices();
    }

    #[Override]
    protected function getButtonColumnContent(Tag $tag): array
    {
        return [
            Button::make()
                ->primary()
                ->icon($tag->locationTag ? 'ban' : 'star')
                ->post([
                    ...Yii::$app->getRequest()->getQueryParams(),
                    $tag->locationTag ? 'delete' : 'create',
                    'location' => $this->provider->location->id,
                    'tag' => $tag->id,
                ]),
        ];
    }
}
