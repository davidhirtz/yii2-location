<?php

namespace davidhirtz\yii2\location\models\queries;

use davidhirtz\yii2\location\models\LocationTag;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use davidhirtz\yii2\skeleton\db\I18nActiveQuery;

class TagQuery extends I18nActiveQuery
{
    public function selectSiteAttributes(): static
    {
        return $this;
    }

    public function withLocationTag(int $locationId, bool $eagerLoading = true, string $joinType = 'LEFT JOIN'): static
    {
        $with = [
            'locationTag' => fn (ActiveQuery $query) => $query->onCondition([LocationTag::tableName() . '.[[location_id]]' => $locationId]),
        ];

        return $this->joinWith($with, $eagerLoading, $joinType);
    }
}