<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Location\Modules\Admin\Controllers\LocationTagController;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Location\Modules\Admin\Interfaces\AutocompleteInterface;
use Hirtz\Skeleton\Helpers\ArrayHelper;
use Hirtz\Skeleton\Modules\Admin\ModuleInterface;
use Hirtz\Skeleton\Widgets\Navs\Nav;
use Hirtz\Skeleton\Widgets\Navs\NavItem;
use Override;
use Yii;

/**
 * @property AutocompleteInterface|null $autocomplete
 * @property \Hirtz\Skeleton\Modules\Admin\Module $module
 */
class Module extends \Hirtz\Skeleton\Base\Module implements ModuleInterface
{
    public $layout = '@skeleton/../resources/views/admin/layouts/main';
    public array|string $url = ['/admin/location/index'];

    #[Override]
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
                'viewPath' => '@location/../resources/views/admin/location',
            ],
            'location-tag' => [
                'class' => LocationTagController::class,
                'viewPath' => '@location/../resources/views/admin/location-tag',
            ],
            'tag' => [
                'class' => TagController::class,
                'viewPath' => '@location/../resources/views/admin/tag',
            ],
        ];
    }

    public function getDashboardPanels(): array
    {
        return [];
    }

    public function aside(Nav $nav): Nav
    {
        return $nav->addItem(NavItem::make()
            ->label($this->getName())
            ->url($this->url)
            ->icon('map-marker-alt')
            ->order(50)
            ->roles([Location::AUTH_LOCATION_UPDATE])
            ->routes(['admin/location/', 'admin/location-tag/', 'admin/tag/']));
    }

    public function getName(): string
    {
        return Yii::t('location', 'Locations');
    }
}
