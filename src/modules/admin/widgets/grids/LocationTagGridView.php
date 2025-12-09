<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Grids;

use Hirtz\Location\models\Tag;
use Hirtz\Skeleton\Helpers\Html;
use Hirtz\Skeleton\Widgets\Fontawesome\Icon;
use Hirtz\Timeago\TimeagoColumn;
use Yii;

class LocationTagGridView extends TagGridView
{
    #[\Override]
    public function init(): void
    {
        if (!$this->rowOptions) {
            $this->rowOptions = fn (Tag $tag) => [
                'class' => $tag->locationTag ? 'is-selected' : null,
            ];
        }

        if (!$this->columns) {
            $this->columns = [
                $this->statusColumn(),
                $this->typeColumn(),
                $this->nameColumn(),
                $this->locationCountColumn(),
                $this->updatedAtColumn(),
                $this->buttonsColumn(),
            ];
        }

        parent::init();
    }


    #[\Override]
    protected function initFooter(): void
    {
        $this->footer = [];
    }

    #[\Override]
    public function updatedAtColumn(): array
    {
        return [
            'class' => TimeagoColumn::class,
            'attribute' => 'locationTag.updated_at',
            'displayAtBreakpoint' => 'lg',
        ];
    }

    #[\Override]
    public function buttonsColumn(): array
    {
        return [
            'contentOptions' => ['class' => 'text-right text-nowrap'],
            'content' => function (Tag $tag): string {
                $route = [
                    ...Yii::$app->getRequest()->getQueryParams(),
                    $tag->locationTag ? 'delete' : 'create',
                    'location' => $this->dataProvider->location->id,
                    'tag' => $tag->id,
                ];

                return Html::buttons(Html::a(Icon::tag($tag->locationTag ? 'ban' : 'star'), $route, [
                    'class' => 'btn btn-primary',
                    'data-method' => 'post',
                ]));
            }
        ];
    }
}
