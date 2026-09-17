<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Modules\Admin\Widgets\Forms;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Modules\Admin\Interfaces\AutocompleteInterface;
use Hirtz\Location\Modules\Admin\Widgets\Forms\LocationProviderIdField;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Widgets\Forms\ActiveForm;
use Yii;

/**
 * An installation with no location provider — the Google bundle missing, or its API key unset — must still be able
 * to edit a location, so the field falls back to the plain input it inherits.
 */
class LocationProviderIdFieldTest extends TestCase
{
    public function testTheFieldIsAPlainInputWithoutAProvider(): void
    {
        $html = $this->render();

        self::assertStringContainsString('name="Location[provider_id]"', $html);
        self::assertStringNotContainsString('hx-get', $html);
        self::assertStringNotContainsString('data-autocomplete', $html);
        self::assertStringNotContainsString('type="search"', $html);
    }

    public function testTheFieldSearchesTheProvider(): void
    {
        $this->setAutocomplete();
        $html = $this->render();

        self::assertStringContainsString('hx-get="/admin/location/location/autocomplete"', $html);
        self::assertStringContainsString('data-autocomplete', $html);
        self::assertStringContainsString('type="search"', $html);
    }

    private function render(): string
    {
        $location = Location::create();
        $location->loadDefaultValues();

        return (string)LocationProviderIdField::make()
            ->form(ActiveForm::make()->model($location));
    }

    private function setAutocomplete(): void
    {
        Yii::$app->getModule('admin')->getModule('location')->set('autocomplete', new class () implements AutocompleteInterface {
            /**
             * @return list<array{text: string, value: mixed}>
             */
            public function getResults(string $input): array
            {
                return [];
            }
        });
    }
}
