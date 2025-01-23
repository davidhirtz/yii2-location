<?php

namespace davidhirtz\yii2\location\modules\admin\assets;

use davidhirtz\yii2\location\modules\admin\controllers\LocationController;
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
