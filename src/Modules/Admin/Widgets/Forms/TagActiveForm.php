<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Forms;

use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Widgets\Forms\ActiveForm;
use Hirtz\Skeleton\Widgets\Forms\Fields\InputField;
use Hirtz\Skeleton\Widgets\Forms\Fields\SelectField;
use Stringable;

/**
 * @property Tag $model
 */
class TagActiveForm extends ActiveForm
{
    protected function configure(): void
    {
        $this->rows ??= [
            $this->getStatusField(),
            $this->getTypeField(),
            $this->getNameField(),
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
}
