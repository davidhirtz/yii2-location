<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Assets;

use Hirtz\Location\Modules\Admin\Controllers\LocationController;
use Hirtz\Skeleton\Assets\AbstractAssetBundle;

/**
 * @see LocationController::actionAutocomplete()
 */
class AutocompleteAssetBundle extends AbstractAssetBundle
{
    public $css = ['css/autocomplete.css'];
    public string $filename = 'js/autocomplete.js';
    public $sourcePath = '@location/../resources/assets/dist';
}
