<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Forms;

use Hirtz\Location\Models\Location;
use Hirtz\Skeleton\Widgets\Forms\ActiveForm;
use Hirtz\Skeleton\Widgets\Forms\Fields\InputField;
use Hirtz\Skeleton\Widgets\Forms\Fields\SelectField;
use Stringable;
use yii\widgets\ActiveField;

/**
 * @property Location $model
 */
class LocationActiveForm extends ActiveForm
{
    protected function configure(): void
    {
        $this->rows ??= [
            [
                $this->getProviderIdField()
            ],
            [
                $this->getStatusField(),
                $this->getTypeField(),
                $this->getNameField(),
                $this->getFormattedAddressField(),
            ],
            [
                $this->getStreetField(),
                $this->getHouseNumberField(),
                $this->getLocalityField(),
                $this->getPostalCodeField(),
                $this->getDistrictField(),
                $this->getCountryCodeField(),
            ],
            [
                $this->getLatField(),
                $this->getLngField(),
            ]
        ];

        parent::configure();
    }

    protected function getStatusField(): ?Stringable
    {
        return SelectField::make()
            ->property('status');
    }

    protected function getTypeField(): ?Stringable
    {
        return SelectField::make()
            ->property('type');
    }

    protected function getNameField(): ?Stringable
    {
        return InputField::make()
            ->property('name');
    }

    protected function getFormattedAddressField(): ?Stringable
    {
        return InputField::make()
            ->property('formatted_address');
    }

    protected function getStreetField(): ?Stringable
    {
        return InputField::make()
            ->property('street');
    }

    protected function getHouseNumberField(): ?Stringable
    {
        return InputField::make()
            ->property('house_number');
    }

    protected function getLocalityField(): ?Stringable
    {
        return InputField::make()
            ->property('locality');
    }

    protected function getPostalCodeField(): ?Stringable
    {
        return InputField::make()
            ->property('postal_code');
    }

    protected function getDistrictField(): ?Stringable
    {
        return InputField::make()
            ->property('district');
    }

    protected function getCountryCodeField(): ?Stringable
    {
        return SelectField::make()
            ->property('country_code')
            ->items($this->getCountyCodeItems());
    }

    protected function getCountyCodeItems(): array
    {
        return $this->model::getCountryCodes();
    }

    protected function getProviderIdField(): ?Stringable
    {
        return LocationProviderIdField::make();
    }

    protected function getLatField(): ?Stringable
    {
        return InputField::make()
            ->property('lat');
    }

    protected function getLngField(): ?Stringable
    {
        return InputField::make()
            ->property('lng');
    }
}
