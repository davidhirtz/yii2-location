<?php

namespace davidhirtz\yii2\location;

use davidhirtz\yii2\location\controllers\ApiController;
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
                'location' => [
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
            'location' => [
                'class' => Module::class,
            ],
        ]);

        /**
         * @see Module::enableApiRoutes()
         * @see ApiController::actionIndex()
         */
        if (Yii::$app->getModules()['location']['enableApiRules'] ?? true) {
            $app->addUrlManagerRules(['api/location/<action>.<format>' => 'location/api/<action>']);
        }

        $app->setMigrationNamespace('davidhirtz\yii2\location\migrations');
    }
}
