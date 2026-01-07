<?php

declare(strict_types=1);

namespace Hirtz\Location;

use Hirtz\Location\controllers\ApiController;
use Hirtz\Skeleton\Web\Application;
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

        $app->getI18n()->translations['location'] ??= [
            'class' => PhpMessageSource::class,
            'basePath' => '@location/../messages',
        ];

        $app->extendModules([
            'admin' => [
                'modules' => [
                    'location' => [
                        'class' => Modules\Admin\Module::class,
                    ],
                ],
            ],
            'location' => [
                'class' => Module::class,
            ],
        ]);

        /**
         * @see Module::$enableApiRoutes
         * @see ApiController::actionIndex()
         */
        if (Yii::$app->getModules()['location']['enableApiRoutes'] ?? true) {
            $app->addUrlManagerRules(['api/location/<action>.<format>' => 'location/api/<action>']);
        }

        $app->setMigrationNamespace('Hirtz\Location\Migrations');
    }
}
