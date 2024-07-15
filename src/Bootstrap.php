<?php

namespace davidhirtz\yii2\location;

use davidhirtz\yii2\skeleton\web\Application;
use Yii;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app): void
    {
        Yii::setAlias('@location', __DIR__);

        $app->extendComponent('i18n', [
            'translations' => [
                'media' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => '@location/messages',
                ],
            ],
        ]);

        $app->extendModules([
            'admin' => [
                'modules' => [
                    'location' => [
                        'class' => modules\admin\Module::class,
                    ],
                ],
            ],
            'media' => [
                'class' => Module::class,
                'uploadPath' => 'uploads'
            ],
        ]);

        $app->setMigrationNamespace('davidhirtz\yii2\location\migrations');
    }
}
