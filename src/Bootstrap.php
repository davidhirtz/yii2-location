<?php

declare(strict_types=1);

namespace Hirtz\Location;

use Hirtz\Location\Controllers\ApiController;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Modules\Admin\Controllers\DashboardController;
use Hirtz\Skeleton\Web\Application;
use Hirtz\Skeleton\Routing\Route;
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
                    'forceTranslation' => true,
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
            $app->addRoutes(Route::to('api/location/{action}.{format}', 'location/api/{action}'));
        }

        DashboardController::addRoles([
            Location::AUTH_LOCATION_UPDATE,
            Tag::AUTH_TAG_UPDATE,
        ]);

        $app->setMigrationNamespace('Hirtz\Location\Migrations');
    }
}
