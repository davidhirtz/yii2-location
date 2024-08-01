<?php

namespace davidhirtz\yii2\location\modules\admin\controllers;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\LocationTag;
use davidhirtz\yii2\location\modules\admin\controllers\traits\LocationTrait;
use davidhirtz\yii2\location\modules\admin\controllers\traits\TagTrait;
use davidhirtz\yii2\location\modules\admin\data\TagActiveDataProvider;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LocationTagController extends Controller
{
    use LocationTrait;
    use TagTrait;
    use ModuleTrait;

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

        $this->errorOrSuccess($locationTag, Yii::t('location', 'The tag was added to the location.'));

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

        $this->errorOrSuccess($locationTag, Yii::t('location', 'The tag was removed to the location.'));

        return $this->redirect(['index'] + Yii::$app->getRequest()->getQueryParams());
    }
}
