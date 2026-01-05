<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules;

use Hirtz\Location\Module;
use Yii;

trait ModuleTrait
{
    public static function getModule(): Module
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('location');
        return $module;
    }
}
