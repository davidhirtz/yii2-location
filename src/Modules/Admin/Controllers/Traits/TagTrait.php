<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Controllers\Traits;

use Hirtz\Location\Models\Tag;
use yii\web\NotFoundHttpException;

trait TagTrait
{
    protected function findTag(int $id): Tag
    {
        $tag = Tag::findOne($id);

        if (!$tag) {
            throw new NotFoundHttpException();
        }

        return $tag;
    }
}
