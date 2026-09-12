<?php

declare(strict_types=1);

namespace Hirtz\Location\Models\Queries;

use Hirtz\Location\Models\LocationTag;
use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Db\ActiveQuery;
use Hirtz\Skeleton\Db\I18nActiveQuery;

/**
 * @extends I18nActiveQuery<Tag>
 */
class TagQuery extends I18nActiveQuery
{
    public function selectSiteAttributes(): static
    {
        return $this;
    }

    public function withLocationTag(int $locationId, string $joinType = 'LEFT JOIN'): static
    {
        return $this->selectWith(
            'locationTag',
            $joinType,
            fn (ActiveQuery $query) => $query->onCondition([LocationTag::tableName() . '.[[location_id]]' => $locationId]),
        );
    }
}
