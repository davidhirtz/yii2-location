<?php

namespace davidhirtz\yii2\location\modules\admin\controllers;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\modules\admin\controllers\traits\LocationTrait;
use davidhirtz\yii2\location\modules\admin\data\LocationActiveDataProvider;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class LocationController extends Controller
{
    use LocationTrait;
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
                        'actions' => ['index', 'update'],
                        'roles' => [Location::AUTH_LOCATION_UPDATE],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => [Location::AUTH_LOCATION_CREATE],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => [Location::AUTH_LOCATION_DELETE],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'order' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(?int $status = null, ?int $type = null, ?string $q = null): Response|string
    {
        $provider = Yii::$container->get(LocationActiveDataProvider::class, [], [
            'status' => $status,
            'type' => $type,
            'search' => $q,
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }

    public function actionCreate(?int $type = null): Response|string
    {
        $location = Location::create();
        $location->loadDefaultValues();
        $location->type ??= $type;

        if (!Yii::$app->getUser()->can(Location::AUTH_LOCATION_CREATE, ['location' => $location])) {
            throw new ForbiddenHttpException();
        }

        if ($location->load(Yii::$app->getRequest()->post()) && $location->insert()) {
            $this->success(Yii::t('location', 'The location was created.'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'location' => $location,
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $location = $this->findLocation($id, Location::AUTH_LOCATION_UPDATE);

        if ($location->load(Yii::$app->getRequest()->post()) && $location->update()) {
            $this->success(Yii::t('location', 'The location was updated.'));
            return $this->refresh();
        }

        return $this->render('update', [
            'location' => $location,
        ]);
    }

    public function actionDelete(int $id): Response|string
    {
        $location = $this->findLocation($id, Location::AUTH_LOCATION_DELETE);

        if ($location->delete()) {
            $this->success(Yii::t('location', 'The location was deleted.'));
            return $this->redirect(['index']);
        }

        $errors = $location->getFirstErrors();
        throw new ServerErrorHttpException(reset($errors));
    }
}
