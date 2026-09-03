<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Controllers;

use Hirtz\Skeleton\I18n\Lang;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\LocationTag;
use Hirtz\Location\Modules\Admin\Controllers\Traits\LocationTrait;
use Hirtz\Location\Modules\Admin\Controllers\Traits\TagTrait;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Location\Modules\Admin\Module;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Web\Controller;
use Override;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @extends Controller<Module>
 */
class LocationTagController extends Controller
{
    use LocationTrait;
    use TagTrait;
    use ModuleTrait;

    #[Override]
    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'delete', 'index'],
                        'roles' => [Location::AUTH_LOCATION_UPDATE],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    #[Override]
    public function beforeAction($action): bool
    {
        if (!self::getModule()->enableTags) {
            throw new NotFoundHttpException();
        }

        return parent::beforeAction($action);
    }

    public function actionIndex(int $location, ?int $status = null, ?int $type = null, ?string $q = null): Response|string
    {
        $location = $this->findLocation($location);

        $provider = Yii::$container->get(TagActiveDataProvider::class, [], [
            'location' => $location,
            'status' => $status,
            'type' => $type,
            'search' => $q,
            'sort' => [
                'defaultOrder' => [
                    Location::instance()->getI18nAttributeName('name') => SORT_ASC,
                ],
            ],
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }

    public function actionCreate(int $location, int $tag): Response|string
    {
        $location = $this->findLocation($location, Location::AUTH_LOCATION_UPDATE);

        $locationTag = LocationTag::create();
        $locationTag->loadDefaultValues();
        $locationTag->load(Yii::$app->getRequest()->post());

        $locationTag->populateLocationRelation($location);
        $locationTag->tag_id = $tag;

        $locationTag->insert();

        $this->errorOrSuccess($locationTag, Lang::t('location', 'LOCATION_TAG_SUCCESS_ADDED'));

        return $this->redirect(['index'] + Yii::$app->getRequest()->getQueryParams());
    }

    public function actionDelete(int $location, int $tag): Response|string
    {
        $locationTag = LocationTag::findOne([
            'location_id' => $location,
            'tag_id' => $tag,
        ]);

        if (!$locationTag) {
            throw new NotFoundHttpException();
        }

        $this->checkLocationPermission($locationTag->location, Location::AUTH_LOCATION_UPDATE);
        $locationTag->delete();

        $this->errorOrSuccess($locationTag, Lang::t('location', 'LOCATION_TAG_SUCCESS_REMOVED'));

        return $this->redirect(['index'] + Yii::$app->getRequest()->getQueryParams());
    }
}
