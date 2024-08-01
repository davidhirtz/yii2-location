<?php

namespace davidhirtz\yii2\location\modules\admin\data;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\LocationTag;
use davidhirtz\yii2\location\models\queries\LocationQuery;
use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use davidhirtz\yii2\skeleton\data\ActiveDataProvider;

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

    public function init(): void
    {
        parent::init();
        $this->initQuery();
    }

    public function initQuery(): void
    {
        if ($this->tag) {
            $this->whereTag();
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

    protected function whereTag(): void
    {
        $this->query->innerJoinWith([
            'locationTag' => function (ActiveQuery $query) {
                $query->onCondition([LocationTag::tableName() . '.[[tag_id]]' => $this->tag->id]);
            }
        ], false);
    }
}
