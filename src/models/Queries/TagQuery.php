<?php

declare(strict_types=1);

namespace Hirtz\Location\Models\Queries;

use Hirtz\Location\Models\LocationTag;
use Hirtz\Skeleton\Db\ActiveQuery;
use Hirtz\Skeleton\Db\I18nActiveQuery;

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
