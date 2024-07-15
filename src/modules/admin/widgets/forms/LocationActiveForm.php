<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\forms;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\modules\admin\widgets\forms\traits\ModelTimestampTrait;
use davidhirtz\yii2\skeleton\modules\admin\widgets\forms\traits\StatusFieldTrait;
use davidhirtz\yii2\skeleton\modules\admin\widgets\forms\traits\TypeFieldTrait;
use davidhirtz\yii2\skeleton\widgets\bootstrap\ActiveForm;
use yii\widgets\ActiveField;

/**
 * @property Location $model
 */
class LocationActiveForm extends ActiveForm
{
    use ModuleTrait;
    use ModelTimestampTrait;
    use StatusFieldTrait;
    use TypeFieldTrait;

    public function init(): void
    {
        $this->fields ??= [
            'status',
            'type',
            'name',
            'formatted_address',
            '-',
            'street',
            'house_number',
            'locality',
            'postal_code',
            'district',
            'state',
            'country',
            '-',
            'lat',
            'lng',
        ];

        parent::init();
    }
}
