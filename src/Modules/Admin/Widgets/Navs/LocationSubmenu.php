<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Location\Models\Location;
use Hirtz\Skeleton\Widgets\Navs\Submenu;
use Override;
use Yii;

class LocationSubmenu extends Submenu
{
    protected ?Location $location = null;

    public function location(Location $location): static
    {
        $this->location = $location;
        return $this;
    }

    #[Override]
    protected function configure(): void
    {
        $this->title ??= Yii::t('location', 'Locations');
        $this->url ??= ['/admin/location/index'];

        parent::configure();
    }
}
