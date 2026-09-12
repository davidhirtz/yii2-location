<?php

declare(strict_types=1);

namespace Hirtz\Location\Models\Queries;

use Hirtz\Location\Models\LocationTag;
use Hirtz\Skeleton\Db\ActiveQuery;
use Hirtz\Skeleton\Db\I18nActiveQuery;

class LocationQuery extends I18nActiveQuery
{
    public function andWhereTagId(int $tagId, bool $eagerLoading = false): static
    {
        $onCondition = fn (ActiveQuery $query) => $query->onCondition([LocationTag::tableName() . '.[[tag_id]]' => $tagId]);

        return $eagerLoading
            ? $this->selectWith('locationTag', 'INNER JOIN', $onCondition)
            : $this->innerJoinWith(['locationTag' => $onCondition], false);
    }
}
