<?php

declare(strict_types=1);

namespace Hirtz\Location\Validators;

use Override;
use yii\validators\FilterValidator;

class CoordinateValidator extends FilterValidator
{
    #[Override]
    public function init(): void
    {
        $this->filter = fn ($attribute): ?string => $attribute
            ? number_format((float)$attribute, 8, '.', '')
            : null;

        parent::init();
    }
}
