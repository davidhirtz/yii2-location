<?php
declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Skeleton\Widgets\Navs\ActionDropdown;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Widgets\Buttons\LocationDeleteButton;
use Override;
use Stringable;

class LocationActionDropdown extends ActionDropdown
{
    /**
     * @use ModelTrait<Location>
     */
    use ModelTrait;

    #[Override]
    protected function configure(): void
    {
        $this->addItem($this->getLocationDeleteButton());
        parent::configure();
    }

    protected function getLocationDeleteButton(): ?Stringable
    {
        return LocationDeleteButton::make()
            ->model($this->model);
    }
}