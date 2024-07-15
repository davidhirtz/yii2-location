<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\grids;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\modules\admin\widgets\grids\GridView;
use davidhirtz\yii2\skeleton\widgets\fontawesome\Icon;
use davidhirtz\yii2\timeago\TimeagoColumn;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * @extends GridView<Location>
 * @property ActiveDataProvider $dataProvider
 */
class LocationGridView extends GridView
{
    use ModuleTrait;

    public function init(): void
    {
        if (!$this->columns) {
            $this->columns = [
                $this->nameColumn(),
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
                    'content' => $this->getSearchInput(),
                    'options' => ['class' => 'col-12 col-md-6'],
                ],
                'options' => [
                    'class' => $this->getModel()::getTypes()
                        ? 'justify-content-between'
                        : 'justify-content-end',
                ],
            ],
        ];
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

                    return $content;
                }

                $name = $address ?: Yii::t('location', 'Unnamed');

                return Html::a($name, $location->getAdminRoute(), ['class' => 'strong']);
            }
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

    public function getModel(): ?Location
    {
        return Location::instance();
    }
}
