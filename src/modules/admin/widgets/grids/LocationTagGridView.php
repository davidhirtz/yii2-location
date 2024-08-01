<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\grids;

use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\widgets\fontawesome\Icon;
use davidhirtz\yii2\timeago\TimeagoColumn;

class LocationTagGridView extends TagGridView
{
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


    protected function initFooter(): void
    {
        $this->footer = [];
    }

    public function updatedAtColumn(): array
    {
        return [
            'class' => TimeagoColumn::class,
            'attribute' => 'locationTag.updated_at',
            'displayAtBreakpoint' => 'lg',
        ];
    }

    public function buttonsColumn(): array
    {
        return [
            'contentOptions' => ['class' => 'text-right text-nowrap'],
            'content' => function (Tag $tag): string {
                $route = [
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
