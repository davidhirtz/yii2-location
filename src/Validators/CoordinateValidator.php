<?php

declare(strict_types=1);

namespace Hirtz\Location\validators;

use yii\validators\FilterValidator;

class CoordinateValidator extends FilterValidator
{
    /**
     * Applies MySQL decimal format.
     */
    #[\Override]
    public function init(): void
    {
        $this->filter = fn ($attribute): ?string => $attribute
            ? number_format((float)$attribute, 8, '.', '')
            : null;

        parent::init();
    }
}
