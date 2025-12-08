<?php

namespace Hirtz\Location\models\queries;

use Hirtz\Location\models\LocationTag;
use Hirtz\Skeleton\db\ActiveQuery;
use Hirtz\Skeleton\db\I18nActiveQuery;

class LocationQuery extends I18nActiveQuery
{
    public function andWhereTagId(int $tagId, bool $eagerLoading = false): static
    {
        return $this->innerJoinWith([
            'locationTag' => fn (ActiveQuery $query) => $query->onCondition([LocationTag::tableName() . '.[[tag_id]]' => $tagId]),
        ], $eagerLoading);
    }
}
