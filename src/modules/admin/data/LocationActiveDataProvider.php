<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Data;

use Hirtz\Location\models\Location;
use Hirtz\Location\models\queries\LocationQuery;
use Hirtz\Location\models\Tag;
use Hirtz\Skeleton\Data\ActiveDataProvider;

/**
 * @property LocationQuery $query
 */
class LocationActiveDataProvider extends ActiveDataProvider
{
    public int $defaultPageSize = 20;

    public ?int $status = null;
    public ?string $search = null;
    public ?Tag $tag = null;
    public ?int $type = null;

    public function __construct($config = [])
    {
        $this->query = Location::find();
        parent::__construct($config);
    }

    #[\Override]
    public function init(): void
    {
        parent::init();
        $this->initQuery();
    }

    public function initQuery(): void
    {
        if ($this->tag) {
            $this->query->andWhereTagId($this->tag->id);
        }

        $this->query->andFilterWhere([
            'status' => $this->status,
            'type' => $this->type,
        ]);

        if ($this->search) {
            $search = $this->query->sanitizeSearchString($this->search);

            $this->query->andFilterWhere([
                'or',
                ['like', $this->query->getI18nAttributeName('name'), $search],
                ['like', $this->query->getI18nAttributeName('formatted_address'), $search],
                ['provider_id' => $search],
            ]);
        }

        $this->setPagination(['defaultPageSize' => $this->defaultPageSize]);
        $this->setSort(['defaultOrder' => ['updated_at' => SORT_DESC]]);
    }
}
