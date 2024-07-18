<?php

namespace davidhirtz\yii2\location\controllers;


use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use davidhirtz\yii2\skeleton\filters\PageCache;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @template T of Location
 * @property Response $response
 */
class ApiController extends Controller
{
    public array $allowedFormats = ['geojson', 'json'];
    public bool $allowAllTypes = true;
    public bool $enablePageCache = true;

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        if ($this->enablePageCache) {
            $behaviors['PageCache'] = [
                'class' => PageCache::class,
                'disableForUsers' => false,
            ];
        }

        return $behaviors;
    }


    public function init(): void
    {
        parent::init();

        $this->enableCsrfValidation = false;
        $this->response->format = Response::FORMAT_JSON;
    }

    /**
     * Checks the `Location::getTypes()` array for a matching slug, sets the `type` parameter accordingly and calls the
     * default action. This allows for URLs like `/api/location/<type-slug>.json`.
     */
    public function runAction($id, $params = [])
    {
        if ($type = $this->findTypeBySlug($id)) {
            $id = $this->defaultAction;
            $params['type'] = $type;
        }

        return parent::runAction($id, $params);
    }

    public function actionIndex(string $format, ?int $type = null): array
    {
        if (!in_array($format, $this->allowedFormats)) {
            throw new BadRequestHttpException();
        }

        if (!$type && !$this->allowAllTypes) {
            throw new NotFoundHttpException();
        }

        return $this->getLocationQuery()
            ->andFilterWhere(['type' => $type])
            ->all();
    }

    protected function getLocationQuery(): ActiveQuery
    {
        $status = Yii::$app->getRequest()->getIsDraft() ? Location::STATUS_DRAFT : Location::STATUS_ENABLED;

        return Location::find()
            ->replaceI18nAttributes()
            ->whereStatus($status);
    }

    protected function findTypeBySlug(string $slug): ?int
    {
        foreach (Location::instance()::getTypes() as $type => $typeOptions) {
            if ($slug === ($typeOptions['slug'] ?? null)) {
                return $type;
            }
        }

        return null;
    }
}