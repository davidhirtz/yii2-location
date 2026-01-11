<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Interfaces;

interface AutocompleteInterface
{
    public function getResults(string $input): array;
}
