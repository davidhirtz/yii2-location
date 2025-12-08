<?php

namespace Hirtz\Location\modules\admin\assets;

use Hirtz\Location\modules\admin\controllers\LocationController;
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
