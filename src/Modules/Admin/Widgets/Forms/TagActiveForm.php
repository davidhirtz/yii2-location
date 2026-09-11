<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Forms;

use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Widgets\Forms\ActiveForm;
use Hirtz\Skeleton\Widgets\Forms\Fields\InputField;
use Hirtz\Skeleton\Widgets\Forms\Fields\SelectField;
use Hirtz\Skeleton\Widgets\Forms\Fields\TypeSelectField;
use Hirtz\Skeleton\Widgets\Forms\Traits\CustomAttributeFieldsTrait;
use Stringable;

/**
 * @property Tag $model
 */
class TagActiveForm extends ActiveForm
{
    use CustomAttributeFieldsTrait;
    #[\Override]
    protected function configure(): void
    {
        $this->rows ??= [
            $this->getStatusField(),
            $this->getTypeField(),
            $this->getNameField(),
            ...$this->getCustomAttributeFields(),
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
        return TypeSelectField::make()
            ->property('type');
    }

    protected function getNameField(): ?Stringable
    {
        return InputField::make()
            ->property('name');
    }
}
