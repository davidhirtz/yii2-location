<?php

namespace davidhirtz\yii2\location\models\queries;

use davidhirtz\yii2\location\models\LocationTag;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use davidhirtz\yii2\skeleton\db\I18nActiveQuery;

class LocationQuery extends I18nActiveQuery
{
    public function andWhereTagId(int $tagId, bool $eagerLoading = false): static
    {
        return $this->innerJoinWith([
            'locationTag' => fn (ActiveQuery $query) => $query->onCondition([LocationTag::tableName() . '.[[tag_id]]' => $tagId]),
        ], $eagerLoading);
    }
}