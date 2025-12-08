<?php

namespace Hirtz\Location\modules\admin\widgets\forms;

use Hirtz\Location\models\Tag;
use Hirtz\Skeleton\modules\admin\widgets\forms\traits\ModelTimestampTrait;
use Hirtz\Skeleton\modules\admin\widgets\forms\traits\StatusFieldTrait;
use Hirtz\Skeleton\modules\admin\widgets\forms\traits\TypeFieldTrait;
use Hirtz\Skeleton\widgets\bootstrap\ActiveForm;

/**
 * @property Tag $model
 */
class TagActiveForm extends ActiveForm
{
    use ModelTimestampTrait;
    use StatusFieldTrait;
    use TypeFieldTrait;

    public function init(): void
    {
        $this->fields ??= [
            'status',
            'type',
            'name',
        ];

        parent::init();
    }
}
