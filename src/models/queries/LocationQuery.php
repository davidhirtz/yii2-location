<?php

namespace davidhirtz\yii2\location\models\queries;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\skeleton\db\I18nActiveQuery;

class LocationQuery extends I18nActiveQuery
{
    public function selectSiteAttributes(): static
    {
        return $this;
    }
}