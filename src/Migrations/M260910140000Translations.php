<?php

declare(strict_types=1);

namespace Hirtz\Location\Migrations;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Db\Traits\MigrationTrait;
use Hirtz\Skeleton\Models\Translation;
use yii\db\Migration;

/**
 * Moves the translated attributes of the location models from their `_xx` columns into {@see Translation} records.
 *
 * @noinspection PhpUnused
 */
class M260910140000Translations extends Migration
{
    use MigrationTrait;

    public function safeUp(): void
    {
        foreach ($this->getModels() as $model) {
            $this->moveI18nColumnsToTranslations($model);
        }
    }

    public function safeDown(): void
    {
        foreach ($this->getModels() as $model) {
            $this->restoreI18nColumnsFromTranslations($model);
        }
    }

    /**
     * @return list<Location|Tag>
     */
    protected function getModels(): array
    {
        return [
            Location::create(),
            Tag::create(),
        ];
    }
}
