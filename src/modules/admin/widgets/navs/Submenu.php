<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\navs;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\location\modules\admin\Module;
use davidhirtz\yii2\location\modules\ModuleTrait;
use Yii;
use yii\helpers\Html;

class Submenu extends \davidhirtz\yii2\skeleton\widgets\fontawesome\Submenu
{
    use ModuleTrait;

    public ?Location $location = null;
    private ?Module $_parentModule = null;

    public function init(): void
    {
        if (!$this->items) {
            $this->items = $this->location ? $this->getLocationItems() : $this->getLocationGridViewItems();
        }

        if ($this->title === null) {
            $module = $this->getParentModule();
            $this->title = Html::a($module->getName(), $module->getRoute());
        }

        $this->setBreadcrumbs();

        parent::init();
    }

    protected function getLocationGridViewItems(): array
    {
        $user = Yii::$app->getUser();

        return [
            [
                'label' => Yii::t('location', 'Locations'),
                'url' => ['location/index'],
                'visible' => $user->can(Location::AUTH_LOCATION_CREATE),
                'active' => ['location/'],
                'icon' => 'map-marker-alt',
                'labelOptions' => [
                    'class' => 'd-none d-md-inline'
                ],
            ],
            [
                'label' => Yii::t('location', 'Tags'),
                'url' => ['tag/index'],
                'visible' => static::getModule()->enableTags && $user->can(Tag::AUTH_TAG_CREATE),
                'active' => ['tag/'],
                'icon' => 'tags',
                'labelOptions' => [
                    'class' => 'd-none d-md-inline'
                ],
            ],
        ];
    }

    protected function getLocationItems(): array
    {
        return [
            [
                'label' => Yii::t('location', 'Location'),
                'url' => $this->location->getAdminRoute(),
                'active' => ['location/'],
                'icon' => 'map-marker-alt',
                'labelOptions' => [
                    'class' => 'd-none d-md-inline'
                ],
            ],
            [
                'label' => Yii::t('location', 'Tags'),
                'url' => ['location-tag/index', 'location' => $this->location->id],
                'visible' => static::getModule()->enableTags,
                'badge' => $this->location->tag_count ?: null,
                'active' => ['location-tag/'],
                'icon' => 'tags',
                'labelOptions' => [
                    'class' => 'd-none d-md-inline'
                ],
            ],
        ];
    }

    protected function setBreadcrumbs(): void
    {
        $view = $this->getView();
        $view->setBreadcrumb($this->getParentModule()->getName(), ['/admin/location/index']);
    }

    protected function getParentModule(): Module
    {
        if ($this->_parentModule === null) {
            /** @var Module $module */
            $module = Yii::$app->getModule('admin')->getModule('location');
            $this->_parentModule = $module;
        }

        return $this->_parentModule;
    }
}
