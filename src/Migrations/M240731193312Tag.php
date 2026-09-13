<?php

declare(strict_types=1);

namespace Hirtz\Location\Migrations;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\LocationTag;
use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Db\Traits\MigrationTrait;
use Hirtz\Skeleton\Models\User;
use Yii;
use yii\db\Migration;

/**
 * The permission names and descriptions this creates are hardcoded: `M2609141[0-6]0000AuthItems` collapses them
 * into one permission per model, so neither the constants nor the message keys exist any more.
 *
 * @noinspection PhpUnused
 */

class M240731193312Tag extends Migration
{
    use MigrationTrait;

    public function safeUp(): void
    {
        $this->createTable(Tag::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'status' => $this->smallInteger()->notNull()->defaultValue(Tag::STATUS_DEFAULT),
            'type' => $this->smallInteger()->notNull()->defaultValue(Tag::TYPE_DEFAULT),
            'name' => $this->string()->notNull(),
            'location_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_by_user_id' => $this->integer()->unsigned()->null(),
            'updated_at' => $this->dateTime(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('name', Tag::tableName(), ['name'], true);

        $this->createTable(LocationTag::tableName(), [
            'location_id' => $this->integer()->unsigned()->notNull(),
            'tag_id' => $this->integer()->unsigned()->notNull(),
            'updated_by_user_id' => $this->integer()->unsigned()->null(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addPrimaryKey('entry_id', LocationTag::tableName(), ['location_id', 'tag_id']);

        $this->addColumn(Location::tableName(), 'tag_ids', (string)$this->json()
            ->null()
            ->after('provider_id'));

        $this->addColumn(Location::tableName(), 'tag_count', (string)$this->integer()
            ->unsigned()
            ->notNull()
            ->defaultValue(0)
            ->after('tag_ids'));

        $auth = Yii::$app->getAuthManager();
        $admin = $auth->getRole(User::AUTH_ROLE_ADMIN);

        $tagUpdate = $auth->createPermission('tagUpdate');
        $tagUpdate->description = 'Update location tags';
        $auth->add($tagUpdate);

        $auth->addChild($admin, $tagUpdate);

        $tagCreate = $auth->createPermission('tagCreate');
        $tagCreate->description = 'Create location tags';
        $auth->add($tagCreate);

        $auth->addChild($admin, $tagCreate);
        $auth->addChild($tagUpdate, $tagCreate);

        $tagDelete = $auth->createPermission('tagDelete');
        $tagDelete->description = 'Delete location tags';
        $auth->add($tagDelete);

        $auth->addChild($admin, $tagDelete);
        $auth->addChild($tagUpdate, $tagDelete);
    }

    public function safeDown(): void
    {
        $auth = Yii::$app->getAuthManager();

        $this->delete($auth->itemTable, ['name' => 'tagDelete']);
        $this->delete($auth->itemTable, ['name' => 'tagCreate']);
        $this->delete($auth->itemTable, ['name' => 'tagUpdate']);

        $this->dropColumn(Location::tableName(), 'tag_ids');
        $this->dropColumn(Location::tableName(), 'tag_count');

        $this->dropTable(LocationTag::tableName());
        $this->dropTable(Tag::tableName());
    }
}
