<?php

declare(strict_types=1);

namespace Hirtz\Location;

use Hirtz\Location\Controllers\ApiController;
use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Models\User;
use Hirtz\Skeleton\Modules\Admin\Controllers\DashboardController;
use Hirtz\Skeleton\Web\Application;
use Yii;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application<User> $app
     */
    public function bootstrap($app): void
    {
        Yii::setAlias('@location', __DIR__);
        TagCollection::reset();

        $app->getI18n()->translations['location'] ??= [
            'class' => PhpMessageSource::class,
            'basePath' => '@location/../messages',
                    'forceTranslation' => true,
];

        $app->extendComponent('search', [
            'models' => [
                Location::class,
                Tag::class,
            ],
        ]);

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

        DashboardController::addRoles(static fn (): array => [
            Location::AUTH_LOCATION,
            Tag::AUTH_TAG,
        ]);

        $app->setMigrationNamespace('Hirtz\Location\Migrations');
    }
}
