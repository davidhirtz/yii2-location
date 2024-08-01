<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\grids;

use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\location\modules\admin\data\TagActiveDataProvider;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\columns\CounterColumn;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\GridView;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\traits\StatusGridViewTrait;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\traits\TypeGridViewTrait;
use davidhirtz\yii2\skeleton\widgets\fontawesome\Icon;
use davidhirtz\yii2\timeago\TimeagoColumn;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * @extends GridView<Tag>
 * @property TagActiveDataProvider $dataProvider
 */
class TagGridView extends GridView
{
    use ModuleTrait;
    use StatusGridViewTrait;
    use TypeGridViewTrait;

    public function init(): void
    {
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

    protected function initHeader(): void
    {
        $this->header ??= [
            [
                [
                    'content' => $this->statusDropdown(),
                    'options' => ['class' => 'col-12 col-md-3'],
                ],
                [
                    'content' => $this->typeDropdown(),
                    'visible' => count($this->getModel()::getTypes()) > 1,
                    'options' => ['class' => 'col-12 col-md-3'],
                ],
                [
                    'content' => $this->getSearchInput(),
                    'options' => ['class' => 'col-12 col-md-6'],
                ],
                'options' => [
                    'class' => 'justify-content-between',
                ],
            ],
        ];
    }

    protected function initFooter(): void
    {
        $this->footer ??= [
            [
                [
                    'content' => $this->getCreateTagButton(),
                    'visible' => Yii::$app->getUser()->can(Tag::AUTH_TAG_CREATE),
                    'options' => ['class' => 'col'],
                ],
            ],
        ];
    }

    protected function getCreateTagButton(): string
    {
        $route = ['/admin/tag/create'];

        return Html::a(Html::iconText('plus', Yii::t('location', 'New Tag')), $route, [
            'class' => 'btn btn-primary',
        ]);
    }

    public function nameColumn(): array
    {
        return [
            'attribute' => 'name',
            'content' => function (Tag $tag) {
                $name = Html::markKeywords($tag->getI18nAttribute('name'), $this->search);
                return Html::a($name, $tag->getAdminRoute(), ['class' => 'strong']);
            }
        ];
    }

    public function locationCountColumn(): array
    {
        return [
            'class' => CounterColumn::class,
            'attribute' => 'location_count',
            'route' => fn (Tag $tag) => ['/admin/location/index', 'tag' => $tag->id],
        ];
    }

    public function updatedAtColumn(): array
    {
        return [
            'attribute' => 'updated_at',
            'class' => TimeagoColumn::class,
        ];
    }

    public function buttonsColumn(): array
    {
        return [
            'contentOptions' => ['class' => 'text-right text-nowrap'],
            'content' => function (Tag $tag): string {
                $button = Html::a(Icon::tag('wrench'), $tag->getAdminRoute(), [
                    'class' => 'btn btn-primary d-none d-md-inline-block',
                ]);

                return Html::buttons($button);
            }
        ];
    }

    public function getModel(): ?Tag
    {
        return Tag::instance();
    }
}
