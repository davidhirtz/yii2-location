<?php

declare(strict_types=1);

namespace Hirtz\Location\Controllers;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Queries\LocationQuery;
use Hirtz\Location\Module;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Filters\PageCache;
use Hirtz\Skeleton\Web\Controller;
use Override;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @extends Controller<Module>
 */
class ApiController extends Controller
{
    use ModuleTrait;

    public array $allowedFormats = ['geojson', 'json'];
    public bool $allowAllTypes = true;
    public bool $enablePageCache = true;

    #[Override]
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        if ($this->enablePageCache) {
            $behaviors['PageCache'] = [
                'class' => PageCache::class,
                'disableForUsers' => false,
                'only' => [],
                'params' => [
                    'action',
                    'format',
                    'tag',
                ],
            ];
        }

        return $behaviors;
    }


    #[Override]
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
    #[Override]
    public function runAction($id, $params = [])
    {
        if ($type = $this->findTypeBySlug($id)) {
            $id = $this->defaultAction;
            $params['type'] = $type;
        }

        return parent::runAction($id, $params);
    }

    public function actionIndex(string $format, ?int $type = null, ?int $tag = null): array
    {
        if (!in_array($format, $this->allowedFormats)) {
            throw new BadRequestHttpException();
        }

        if (!$type && !$this->allowAllTypes) {
            throw new NotFoundHttpException();
        }

        $query = $this->getLocationQuery()
            ->andFilterWhere(['type' => $type]);

        if ($tag && static::getModule()->enableTags) {
            $query->andWhereTagId($tag);
        }

        return $query->all();
    }

    protected function getLocationQuery(): LocationQuery
    {
        $status = $this->request->getIsDraft() ? Location::STATUS_DRAFT : Location::STATUS_ENABLED;

        return Location::find()
            ->withTranslations()
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
