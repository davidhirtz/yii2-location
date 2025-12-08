<?php

namespace Hirtz\Location\modules;

use Hirtz\Location\Module;
use Yii;

trait ModuleTrait
{
    protected static ?Module $_module = null;

    public static function getModule(): Module
    {
        if (static::$_module === null) {
            /** @var Module $module */
            $module = Yii::$app->getModule('location');
            static::$_module ??= $module;
        }

        return static::$_module;
    }
}
