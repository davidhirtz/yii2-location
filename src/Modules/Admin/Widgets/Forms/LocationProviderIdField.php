<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Forms;

use Hirtz\Location\Modules\Admin\Module;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Widgets\Forms\Fields\AutocompleteField;
use Override;
use Yii;

class LocationProviderIdField extends AutocompleteField
{
    use ModuleTrait;

    public ?string $property = 'provider_id';

    #[Override]
    protected function configure(): void
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('admin')->getModule('location');

        // Without a provider there is nothing to search, so the field stays the plain input it renders on its own.
        if ($module->getAutocomplete()) {
            $this->url ??= ['/admin/location/location/autocomplete'];

            if (!$this->model->{$this->property}) {
                $this->attributes['placeholder'] ??= Yii::t('location', 'LOCATION_PROVIDER_ID_SEARCH');
                $this->attributes['type'] ??= 'search';
            }
        }

        parent::configure();
    }
}
