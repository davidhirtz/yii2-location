<?php

declare(strict_types=1);

namespace Hirtz\Location;

use Hirtz\Location\Controllers\ApiController;
use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Base\ConfigBootstrapInterface;
use Hirtz\Skeleton\Models\User;
use Hirtz\Skeleton\Modules\Admin\Controllers\DashboardController;
use Hirtz\Skeleton\Web\Application;
use Override;
use Yii;
use yii\i18n\PhpMessageSource;

class Bootstrap implements ConfigBootstrapInterface
{
    #[Override]
    public static function getDefaultConfig(): array
    {
        return [
            'components' => [
                'i18n' => [
                    'translations' => [
                        'location' => [
                            'class' => PhpMessageSource::class,
                            'basePath' => '@location/../messages',
                            'forceTranslation' => true,
                        ],
                    ],
                ],
                'search' => [
                    'models' => [
                        Location::class,
                        Tag::class,
                    ],
                ],
            ],
            'modules' => [
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
            ],
        ];
    }

    /**
     * @param Application<User> $app
     */
    public function bootstrap($app): void
    {
        Yii::setAlias('@location', __DIR__);
        TagCollection::reset();

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
