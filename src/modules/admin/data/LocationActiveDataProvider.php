<?php

namespace davidhirtz\yii2\location\modules\admin\data;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use yii\data\ActiveDataProvider;

/**
 * @property ActiveQuery $query
 */
class LocationActiveDataProvider extends ActiveDataProvider
{
    public ?int $status = null;
    public ?string $search = null;
    public ?int $type = null;

    public function init(): void
    {
        $this->initQuery();
        parent::init();
    }

    public function initQuery(): void
    {
        $this->query ??= Location::find();

        $this->query->andFilterWhere([
            'status' => $this->status,
            'type' => $this->type,
        ]);

        if ($this->search) {
            $search = $this->query->sanitizeSearchString($this->search);

            $this->query->andFilterWhere([
                'or',
                ['like', 'name', $search],
                ['like', 'formatted_address', $search],
                'provider_id' => $search,
            ]);
        }

        $this->setPagination(['defaultPageSize' => 20]);
        $this->setSort(['defaultOrder' => ['updated_at' => SORT_DESC]]);
    }
}
