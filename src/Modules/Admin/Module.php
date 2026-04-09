<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin;

use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Location\Modules\Admin\Controllers\LocationTagController;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Location\Modules\Admin\Interfaces\AutocompleteInterface;
use Hirtz\Location\Modules\Admin\Widgets\Navs\LocationNavItem;
use Hirtz\Skeleton\Helpers\ArrayHelper;
use Hirtz\Skeleton\Modules\Admin\ModuleInterface;
use Hirtz\Skeleton\Widgets\Navs\Nav;
use Override;

/**
 * @property AutocompleteInterface|null $autocomplete
 * @property \Hirtz\Skeleton\Modules\Admin\Module $module
 */
class Module extends \Hirtz\Skeleton\Base\Module implements ModuleInterface
{
    public $layout = '@skeleton/../resources/views/admin/layouts/main';

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

    public function beforeAction($action): bool
    {
        $this->setViewPath('@location/../resources/views/admin/');
        return parent::beforeAction($action);
    }

    protected function getCoreControllerMap(): array
    {
        return [
            'location' => LocationController::class,
            'location-tag' => LocationTagController::class,
            'tag' => TagController::class,
        ];
    }

    public function getDashboardPanels(): array
    {
        return [];
    }

    public function aside(Nav $nav): Nav
    {
        return $nav->addItem(LocationNavItem::make());
    }
}
