<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Controllers\Traits;

use Hirtz\Location\Models\Tag;
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

        if ($permissionName && !$this->webuser->can($permissionName, ['tag' => $tag])) {
            throw new ForbiddenHttpException();
        }

        return $tag;
    }
}
