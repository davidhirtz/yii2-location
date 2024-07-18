<?php

namespace davidhirtz\yii2\location\modules\admin\assets;

use davidhirtz\yii2\location\modules\admin\controllers\LocationController;
use davidhirtz\yii2\skeleton\assets\JuiAsset;
use yii\web\AssetBundle;

/**
 * @see LocationController::actionAutocomplete()
 */
class AutocompleteAssetBundle extends AssetBundle
{
    public $depends = [JuiAsset::class];
    public $js = ['autocomplete.min.js'];
    public $sourcePath = '@location/modules/admin/assets/autocomplete/dist';
}