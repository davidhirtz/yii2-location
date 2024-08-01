<?php

namespace davidhirtz\yii2\location\modules\admin;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\modules\admin\controllers\LocationController;
use davidhirtz\yii2\location\modules\admin\controllers\LocationTagController;
use davidhirtz\yii2\location\modules\admin\controllers\TagController;
use davidhirtz\yii2\location\modules\admin\interfaces\AutocompleteInterface;
use davidhirtz\yii2\skeleton\helpers\ArrayHelper;
use davidhirtz\yii2\skeleton\modules\admin\ModuleInterface;
use Yii;

/**
 * @property AutocompleteInterface|null $autocomplete
 * @property \davidhirtz\yii2\skeleton\modules\admin\Module $module
 */
class Module extends \davidhirtz\yii2\skeleton\base\Module implements ModuleInterface
{
    public $layout = '@skeleton/modules/admin/views/layouts/main';

    public function init(): void
    {
        $this->controllerMap = ArrayHelper::merge($this->getCoreControllerMap(), $this->controllerMap);
        parent::init();
    }

    public function getAutocomplete(): ?AutocompleteInterface
    {
        return $this->get('autocomplete', false);
    }

    protected function getCoreControllerMap(): array
    {
        return [
            'location' => [
                'class' => LocationController::class,
                'viewPath' => '@location/modules/admin/views/location',
            ],
            'location-tag' => [
                'class' => LocationTagController::class,
                'viewPath' => '@location/modules/admin/views/location-tag',
            ],
            'tag' => [
                'class' => TagController::class,
                'viewPath' => '@location/modules/admin/views/tag',
            ],
        ];
    }

    public function getDashboardPanels(): array
    {
        return [];
    }

    public function getName(): string
    {
        return Yii::t('location', 'Locations');
    }

    public function getRoute(): array
    {
        return ['/admin/location/index'];
    }

    public function getNavBarItems(): array
    {
        return [
            'location' => [
                'label' => $this->getName(),
                'icon' => 'map-marker-alt',
                'url' => $this->getRoute(),
                'active' => [
                    'admin/location/',
                    'admin/location-tag/',
                    'admin/tag/'
                ],
                'roles' => [
                    Location::AUTH_LOCATION_UPDATE,
                ],
            ],
        ];
    }
}
