<?php

namespace davidhirtz\yii2\location\modules\admin\controllers\traits;

use davidhirtz\yii2\location\models\Tag;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

trait TagTrait
{
    protected function findTag(int $id, ?string $permissionName = null): Tag
    {
        $tag = Tag::findOne($id);

        if (!$tag) {
            throw new NotFoundHttpException();
        }

        if ($permissionName && !Yii::$app->getUser()->can($permissionName, ['tag' => $tag])) {
            throw new ForbiddenHttpException();
        }

        return $tag;
    }
}
