<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\grids;

use davidhirtz\yii2\location\models\collections\TagCollection;
use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\location\modules\admin\data\LocationActiveDataProvider;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\helpers\Url;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\columns\CounterColumn;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\GridView;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\traits\StatusGridViewTrait;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\traits\TypeGridViewTrait;
use davidhirtz\yii2\skeleton\widgets\bootstrap\ButtonDropdown;
use davidhirtz\yii2\skeleton\widgets\fontawesome\Icon;
use davidhirtz\yii2\timeago\TimeagoColumn;
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

    /**
     * @var bool whether tags be selectable via dropdown
     */
    public bool $showTagDropdown = true;

    /**
     * @var bool whether tags should be displayed in the grid
     */
    public bool $showTags = true;

    /**
     * @var bool whether location types should be selectable via dropdown
     */
    public bool $showTypeDropdown = true;

    public function init(): void
    {
        if (!$this->columns) {
            $this->columns = [
                $this->statusColumn(),
                $this->typeColumn(),
                $this->nameColumn(),
                $this->tagCountColumn(),
                $this->updatedAtColumn(),
                $this->buttonsColumn(),
            ];
        }

        if ($this->showTags) {
            $this->showTags = static::getModule()->enableTags;
        }

        if ($this->showTagDropdown) {
            $this->showTagDropdown = static::getModule()->enableTags && count(TagCollection::getAll()) > 1;
        }

        if ($this->showTypeDropdown) {
            $this->showTypeDropdown = count($this->getModel()::getTypes()) > 1;
        }

        parent::init();
    }

    protected function initHeader(): void
    {
        $this->header ??= [
            [
                [
                    'content' => $this->typeDropdown(),
                    'visible' => $this->showTypeDropdown,
                    'options' => ['class' => 'col-12 col-md-3'],
                ],
                [
                    'content' => $this->tagDropdown(),
                    'options' => ['class' => 'col-12 col-md-3'],
                    'visible' => $this->showTagDropdown,
                ],
                [
                    'content' => $this->getSearchInput(),
                    'options' => ['class' => 'col-12 col-md-6'],
                ],
                'options' => [
                    'class' => $this->showTypeDropdown || $this->showTagDropdown ? 'justify-content-between' : 'justify-content-end',
                ],
            ],
        ];
    }

    protected function tagDropdown(): string
    {
        $items = [];

        foreach (TagCollection::getAll() as $tag) {
            $items[] = [
                'label' => $tag->getI18nAttribute('name'),
                'url' => $this->getTagUrl($tag),
            ];
        }

        return ButtonDropdown::widget([
            'label' => $this->dataProvider->tag?->getI18nAttribute('name') ?? Yii::t('skeleton', 'Tags'),
            'items' => $items,
            'paramName' => 'tag',
        ]);
    }

    protected function initFooter(): void
    {
        $this->footer ??= [
            [
                [
                    'content' => $this->getCreateLocationButton(),
                    'visible' => Yii::$app->getUser()->can(Location::AUTH_LOCATION_CREATE),
                    'options' => ['class' => 'col'],
                ],
            ],
        ];
    }

    protected function getCreateLocationButton(): string
    {
        $route = ['/admin/location/create'];

        return Html::a(Html::iconText('plus', Yii::t('location', 'New Location')), $route, [
            'class' => 'btn btn-primary',
        ]);
    }

    public function nameColumn(): array
    {
        return [
            'attribute' => 'name',
            'content' => function (Location $location) {
                if ($address = $location->formatted_address) {
                    $address = Html::markKeywords(Html::encode($address), $this->getSearchKeywords());
                }

                if ($name = $location->getI18nAttribute('name')) {
                    $name = Html::markKeywords(Html::encode($name), $this->getSearchKeywords());
                    $content = Html::a($name, $location->getAdminRoute(), ['class' => 'strong']);

                    if ($address) {
                        $content .= Html::tag('div', $address, [
                            'class' => 'small',
                        ]);
                    }
                } else {
                    $content = $address ?: Yii::t('location', 'Unnamed');
                    $content = Html::a($content, $location->getAdminRoute(), ['class' => 'strong']);
                }

                if ($this->showTags) {
                    $content .= $this->renderTagButtons($location);
                }

                return $content;
            }
        ];
    }

    public function tagCountColumn(): array
    {
        return [
            'class' => CounterColumn::class,
            'attribute' => 'tag_count',
            'visible' => static::getModule()->enableTags,
            'route' => fn (Location $location) => ['/admin/location-tag/index', 'location' => $location->id],
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
            'content' => function (Location $location): string {
                $button = Html::a(Icon::tag('wrench'), $location->getAdminRoute(), [
                    'class' => 'btn btn-primary d-none d-md-inline-block',
                ]);

                return Html::buttons($button);
            }
        ];
    }

    public function renderTagButtons(Location $location, array $options = []): string
    {
        $tags = [];

        foreach (TagCollection::getByLocation($location) as $tag) {
            $tags[] = Html::a(Html::encode($tag->getI18nAttribute('name')), $this->getTagUrl($tag), [
                'class' => 'btn btn-secondary btn-sm',
            ]);
        }

        return $tags
            ? Html::tag('div', implode('', $tags), $options ?: ['class' => 'btn-list'])
            : '';
    }

    protected function getTagUrl(Tag $tag): string
    {
        return Url::current([
            'tag' => $tag->id,
            'page' => null,
        ]);
    }

    public function getModel(): ?Location
    {
        return Location::instance();
    }
}
