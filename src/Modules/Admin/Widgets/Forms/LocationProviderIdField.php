<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Forms;

use Hirtz\Location\Modules\Admin\Assets\AutocompleteAssetBundle;
use Hirtz\Location\Modules\Admin\Module;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Widgets\Forms\Fields\InputField;
use Override;
use Yii;

class LocationProviderIdField extends InputField
{
    use ModuleTrait;

    #[Override]
    protected function configure(): void
    {
        $this->property ??= 'provider_id';

        $this->attributes['autocomplete'] ??= 'off';

        if (!$this->model->{$this->property}) {
            $this->attributes['placeholder'] ??= Yii::t('location', 'LOCATION_PROVIDER_ID_SEARCH');
            $this->attributes['type'] ??= 'search';
        }


        /** @var Module $module */
        $module = Yii::$app->getModule('admin')->getModule('location');

        if ($module->getAutocomplete()) {
            $this->registerAutocompleteClientScript();
        }

        parent::configure();
    }

    protected function registerAutocompleteClientScript(): void
    {
        $module = AutocompleteAssetBundle::register($this->view);

        $this->view->registerJsModule("$module->baseUrl/$module->filename", [
            '#' . $this->getId(),
            Yii::$app->getUrlManager()->createUrl(['/admin/location/location/autocomplete']),
        ]);
    }
}
