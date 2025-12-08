<?php

namespace Hirtz\Location\modules\admin;

use Hirtz\Location\models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Location\Modules\Admin\Controllers\LocationTagController;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Location\Modules\Admin\Interfaces\AutocompleteInterface;
use Hirtz\Skeleton\Helpers\ArrayHelper;
use Hirtz\Skeleton\Modules\Admin\Config\MainMenuItemConfig;
use Hirtz\Skeleton\Modules\Admin\ModuleInterface;
use Yii;

/**
 * @property AutocompleteInterface|null $autocomplete
 * @property \Hirtz\Skeleton\Modules\Admin\Module $module
 */
class Module extends \Hirtz\Skeleton\Base\Module implements ModuleInterface
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
