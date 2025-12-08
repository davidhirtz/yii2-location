<?php

namespace Hirtz\Location\Modules\Admin\Assets;

use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use yii\web\AssetBundle;

/**
 * @todo
 * @see LocationController::actionAutocomplete()
 */
class AutocompleteAssetBundle extends AssetBundle
{
    public $js = ['autocomplete.min.js'];
    public $sourcePath = '@location/modules/admin/assets/autocomplete/dist';
}
