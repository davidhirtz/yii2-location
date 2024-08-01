<?php

namespace davidhirtz\yii2\location\modules\admin\data;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\queries\TagQuery;
use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\skeleton\data\ActiveDataProvider;

/**
 * @property TagQuery $query
 */
class TagActiveDataProvider extends ActiveDataProvider
{
    public ?Location $location = null;
    public ?int $status = null;
    public ?string $search = null;
    public ?int $type = null;
    public int $defaultPageSize = 20;

    public function __construct($config = [])
    {
        $this->query = Tag::find();
        parent::__construct($config);
    }

    public function init(): void
    {
        parent::init();
        $this->initQuery();
    }

    public function initQuery(): void
    {
        if ($this->location) {
            $this->query->withLocationTag($this->location->id);
        }

        $this->query->andFilterWhere([
            'status' => $this->status,
            'type' => $this->type,
        ]);

        if ($this->search) {
            $search = $this->query->sanitizeSearchString($this->search);
            $this->query->andWhere(['like', $this->query->getI18nAttributeName('name'), $search]);
        }

        $this->setPagination(['defaultPageSize' => $this->defaultPageSize]);
    }
}
