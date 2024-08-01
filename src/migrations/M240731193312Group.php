<?php

namespace davidhirtz\yii2\location\migrations;

use davidhirtz\yii2\location\migrations\traits\I18nTablesTrait;
use davidhirtz\yii2\location\models\Group;
use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\LocationGroup;
use davidhirtz\yii2\skeleton\db\traits\MigrationTrait;
use davidhirtz\yii2\skeleton\models\User;
use Yii;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */

class M240731193312Group extends Migration
{
    use MigrationTrait;
    use I18nTablesTrait;

    public function safeUp(): void
    {
        $this->i18nTablesCallback(function () {
            $this->createTable(Group::tableName(), [
                'id' => $this->primaryKey()->unsigned(),
                'status' => $this->smallInteger()->notNull()->defaultValue(Location::STATUS_DEFAULT),
                'type' => $this->smallInteger()->notNull()->defaultValue(Location::TYPE_DEFAULT),
                'name' => $this->string()->notNull(),
                'location_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
                'updated_by_user_id' => $this->integer()->unsigned()->null(),
                'updated_at' => $this->dateTime(),
                'created_at' => $this->dateTime()->notNull(),
            ]);

            $group = Group::create();
            $this->addI18nColumns(Group::tableName(), $group->i18nAttributes);

            foreach ($group->getI18nAttributesNames(['name']) as $attributeName) {
                $this->createIndex($attributeName, Group::tableName(), [$attributeName], true);
            }

            $this->createTable(LocationGroup::tableName(), [
                'location_id' => $this->integer()->unsigned()->notNull(),
                'group_id' => $this->integer()->unsigned()->notNull(),
                'position' => $this->integer()->unsigned()->notNull()->defaultValue(0),
                'updated_by_user_id' => $this->integer()->unsigned()->null(),
                'updated_at' => $this->dateTime(),
            ]);

            $this->addColumn(Location::tableName(), 'group_ids', $this->json()
                ->null()
                ->after('provider_id'));

            $this->addColumn(Location::tableName(), 'group_count', $this->integer()
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->after('group_ids'));
        });

        $auth = Yii::$app->getAuthManager();
        $admin = $auth->getRole(User::AUTH_ROLE_ADMIN);

        $groupUpdate = $auth->createPermission(Group::AUTH_GROUP_UPDATE);
        $groupUpdate->description = Yii::t('location', 'Update location groups', [], Yii::$app->sourceLanguage);
        $auth->add($groupUpdate);

        $auth->addChild($admin, $groupUpdate);

        $groupCreate = $auth->createPermission(Group::AUTH_GROUP_CREATE);
        $groupCreate->description = Yii::t('location', 'Create location groups', [], Yii::$app->sourceLanguage);
        $auth->add($groupCreate);

        $auth->addChild($admin, $groupCreate);
        $auth->addChild($groupUpdate, $groupCreate);

        $groupDelete = $auth->createPermission(Group::AUTH_GROUP_DELETE);
        $groupDelete->description = Yii::t('location', 'Delete location groups', [], Yii::$app->sourceLanguage);
        $auth->add($groupDelete);

        $auth->addChild($admin, $groupDelete);
        $auth->addChild($groupUpdate, $groupDelete);
    }

    public function safeDown(): void
    {
        $auth = Yii::$app->getAuthManager();

        $this->delete($auth->itemTable, ['name' => Group::AUTH_GROUP_DELETE]);
        $this->delete($auth->itemTable, ['name' => Group::AUTH_GROUP_CREATE]);
        $this->delete($auth->itemTable, ['name' => Group::AUTH_GROUP_UPDATE]);

        $this->i18nTablesCallback(function () {
            $this->dropColumn(Location::tableName(), 'group_ids');
            $this->dropColumn(Location::tableName(), 'group_count');

            $this->dropTable(LocationGroup::tableName());
            $this->dropTable(Group::tableName());
        });
    }
}