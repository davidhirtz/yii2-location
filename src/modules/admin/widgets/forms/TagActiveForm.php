<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Forms;

use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Modules\Admin\Widgets\Forms\Traits\ModelTimestampTrait;
use Hirtz\Skeleton\Modules\Admin\Widgets\Forms\Traits\StatusFieldTrait;
use Hirtz\Skeleton\Modules\Admin\Widgets\Forms\Traits\TypeFieldTrait;
use Hirtz\Skeleton\Widgets\Bootstrap\ActiveForm;

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
