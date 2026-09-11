<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Controllers;

use Hirtz\Skeleton\I18n\Lang;
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\Traits\LocationTrait;
use Hirtz\Location\Modules\Admin\Data\LocationActiveDataProvider;
use Hirtz\Location\Modules\Admin\Module;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Web\Controller;
use Override;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * @extends Controller<Module>
 */
class LocationController extends Controller
{
    use LocationTrait;
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
                        'actions' => ['autocomplete', 'index', 'update'],
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

    public function actionIndex(?int $tag = null, ?int $status = null, ?int $type = null, ?string $q = null): Response|string
    {
        $provider = Yii::$container->get(LocationActiveDataProvider::class, [], [
            'search' => $q,
            'status' => $status,
            'tag' => Tag::findOne($tag),
            'type' => $type,
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

        if ($location->load(Yii::$app->getRequest()->post()) && !$this->request->isFormReload() && $location->insert()) {
            $this->success(Lang::t('location', 'LOCATION_SUCCESS_CREATED'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'location' => $location,
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $location = $this->findLocation($id, Location::AUTH_LOCATION_UPDATE);

        if ($location->load(Yii::$app->getRequest()->post()) && !$this->request->isFormReload() && $location->update()) {
            $this->success(Lang::t('location', 'LOCATION_SUCCESS_UPDATED'));
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
            $this->success(Lang::t('location', 'LOCATION_SUCCESS_DELETED'));
            return $this->redirect(['index']);
        }

        $errors = $location->getFirstErrors();
        throw new ServerErrorHttpException(reset($errors));
    }

    public function actionAutocomplete(string $q): Response
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('admin')->getModule('location');
        $autocomplete = $module->getAutocomplete();

        return $this->asJson($autocomplete?->getResults($q) ?? []);
    }
}
