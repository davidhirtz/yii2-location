<?php

namespace Hirtz\Location\Modules\Admin\Interfaces;

interface AutocompleteInterface
{
    public function getResults(string $term): array;
}
