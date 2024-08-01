<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\forms;

use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\skeleton\modules\admin\widgets\forms\traits\ModelTimestampTrait;
use davidhirtz\yii2\skeleton\modules\admin\widgets\forms\traits\StatusFieldTrait;
use davidhirtz\yii2\skeleton\modules\admin\widgets\forms\traits\TypeFieldTrait;
use davidhirtz\yii2\skeleton\widgets\bootstrap\ActiveForm;

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
