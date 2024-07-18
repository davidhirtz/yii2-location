<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\forms;

use davidhirtz\yii2\location\models\Location;
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
    use ModelTimestampTrait;
    use StatusFieldTrait;
    use TypeFieldTrait;

    public function init(): void
    {
        $this->fields ??= [
            'provider_id',
            '-',
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
            'country_code',
            '-',
            'lat',
            'lng',
        ];

        parent::init();
    }

    /**
     * @noinspection PhpUnused {@see self::renderFields()}
     */
    public function countryCodeField(array $options = []): ActiveField|string
    {
        $items = $this->getCountyCodeItems();

        if (count($items) < 2) {
            return '';
        }

        if (!$this->model->isAttributeRequired('country_code')) {
            $options['inputOptions']['prompt'] ??= '';
        }

        return $this->field($this->model, 'country_code', $options)->dropDownList($items);
    }

    protected function getCountyCodeItems(): array
    {
        return $this->model::getCountryCodes();
    }

    /**
     * @noinspection PhpUnused {@see self::renderFields()}
     */
    public function providerIdField(array $options = []): ActiveField|string
    {
        return $this->field($this->model, 'provider_id')
            ->widget(AutocompleteInputWidget::class, $options);
    }
}
