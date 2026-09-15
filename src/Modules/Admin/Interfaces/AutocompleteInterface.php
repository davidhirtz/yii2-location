<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Interfaces;

interface AutocompleteInterface
{
    /**
     * @return list<array{text: string, value: mixed}>
     */
    public function getResults(string $input): array;
}
