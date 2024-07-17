<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\navs;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\modules\admin\Module;
use davidhirtz\yii2\location\modules\ModuleTrait;
use Yii;
use yii\helpers\Html;

class Submenu extends \davidhirtz\yii2\skeleton\widgets\fontawesome\Submenu
{
    use ModuleTrait;

    private ?Module $_parentModule = null;

    public function init(): void
    {
        if (!$this->items) {
            $user = Yii::$app->getUser();

            $this->items = [
                [
                    'label' => Yii::t('location', 'Locations'),
                    'url' => ['location/index'],
                    'visible' => $user->can(Location::AUTH_LOCATION_CREATE),
                    'active' => ['location/'],
                    'icon' => 'images',
                    'labelOptions' => [
                        'class' => 'd-none d-md-inline'
                    ],
                ],
            ];
        }

        if ($this->title === null) {
            $module = $this->getParentModule();
            $this->title = Html::a($module->getName(), $module->getRoute());
        }

        $this->setBreadcrumbs();

        parent::init();
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
