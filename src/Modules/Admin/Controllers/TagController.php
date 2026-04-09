<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Controllers;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\Traits\TagTrait;
use Hirtz\Location\Modules\Admin\Data\TagActiveDataProvider;
use Hirtz\Location\Modules\Admin\Module;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * @extends Controller<Module>
 */
class TagController extends Controller
{
    use TagTrait;
    use ModuleTrait;

    #[\Override]
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
                        'roles' => [Tag::AUTH_TAG_UPDATE],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => [Tag::AUTH_TAG_CREATE],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => [Tag::AUTH_TAG_DELETE],
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
        $provider = Yii::$container->get(TagActiveDataProvider::class, [], [
            'status' => $status,
            'type' => $type,
            'search' => $q,
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }

    public function actionCreate(?int $type = null): Response|string
    {
        $tag = Tag::create();
        $tag->loadDefaultValues();
        $tag->type ??= $type;

        if (!Yii::$app->getUser()->can(Tag::AUTH_TAG_CREATE, ['tag' => $tag])) {
            throw new ForbiddenHttpException();
        }

        if ($tag->load(Yii::$app->getRequest()->post()) && $tag->insert()) {
            $this->success(Yii::t('location', 'The tag was created.'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'tag' => $tag,
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $tag = $this->findTag($id, Tag::AUTH_TAG_UPDATE);

        if ($tag->load(Yii::$app->getRequest()->post()) && $tag->update()) {
            $this->success(Yii::t('location', 'The tag was updated.'));
            return $this->refresh();
        }

        return $this->render('update', [
            'tag' => $tag,
        ]);
    }

    public function actionDelete(int $id): Response|string
    {
        $tag = $this->findTag($id, Tag::AUTH_TAG_DELETE);

        if ($tag->delete()) {
            $this->success(Yii::t('location', 'The tag was deleted.'));
            return $this->redirect(['index']);
        }

        $errors = $tag->getFirstErrors();
        throw new ServerErrorHttpException(reset($errors));
    }
}
