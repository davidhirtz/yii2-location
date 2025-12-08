<?php

namespace Hirtz\Location\modules\admin;

use Hirtz\Location\models\Location;
use Hirtz\Location\modules\admin\controllers\LocationController;
use Hirtz\Location\modules\admin\controllers\LocationTagController;
use Hirtz\Location\modules\admin\controllers\TagController;
use Hirtz\Location\modules\admin\interfaces\AutocompleteInterface;
use Hirtz\Skeleton\helpers\ArrayHelper;
use Hirtz\Skeleton\modules\admin\config\MainMenuItemConfig;
use Hirtz\Skeleton\modules\admin\ModuleInterface;
use Yii;

/**
 * @property AutocompleteInterface|null $autocomplete
 * @property \Hirtz\Skeleton\modules\admin\Module $module
 */
class Module extends \Hirtz\Skeleton\base\Module implements ModuleInterface
{
    public $layout = '@skeleton/modules/admin/views/layouts/main';
    public array|string $url = ['/admin/location/index'];

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

    public function getMainMenuItems(): array
    {
        return [
            'location' => new MainMenuItemConfig(
                label: $this->getName(),
                url: $this->url,
                icon: 'map-marker-alt',
                roles: [
                    Location::AUTH_LOCATION_UPDATE,
                ],
                routes: [
                    'admin/location/',
                    'admin/location-tag/',
                    'admin/tag/'
                ],
            ),
        ];
    }
}
