<?php

namespace davidhirtz\yii2\location\modules\admin\interfaces;

interface AutocompleteInterface
{
    public function getResults(string $term): array;
}