<?php

namespace Hirtz\Location\modules\admin\interfaces;

interface AutocompleteInterface
{
    public function getResults(string $term): array;
}
