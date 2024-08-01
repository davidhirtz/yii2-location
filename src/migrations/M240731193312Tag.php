<?php

namespace davidhirtz\yii2\location\migrations;

use davidhirtz\yii2\location\migrations\traits\I18nTablesTrait;
use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\LocationTag;
use davidhirtz\yii2\skeleton\db\traits\MigrationTrait;
use davidhirtz\yii2\skeleton\models\User;
use Yii;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */

class M240731193312Tag extends Migration
{
    use MigrationTrait;
    use I18nTablesTrait;

    public function safeUp(): void
    {
        $this->i18nTablesCallback(function () {
            $this->createTable(Tag::tableName(), [
                'id' => $this->primaryKey()->unsigned(),
                'status' => $this->smallInteger()->notNull()->defaultValue(Location::STATUS_DEFAULT),
                'type' => $this->smallInteger()->notNull()->defaultValue(Location::TYPE_DEFAULT),
                'name' => $this->string()->notNull(),
                'location_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
                'updated_by_user_id' => $this->integer()->unsigned()->null(),
                'updated_at' => $this->dateTime(),
                'created_at' => $this->dateTime()->notNull(),
            ]);

            $tag = Tag::create();
            $this->addI18nColumns(Tag::tableName(), $tag->i18nAttributes);

            foreach ($tag->getI18nAttributesNames(['name']) as $attributeName) {
                $this->createIndex($attributeName, Tag::tableName(), [$attributeName], true);
            }

            $this->createTable(LocationTag::tableName(), [
                'location_id' => $this->integer()->unsigned()->notNull(),
                'tag_id' => $this->integer()->unsigned()->notNull(),
                'position' => $this->integer()->unsigned()->notNull()->defaultValue(0),
                'updated_by_user_id' => $this->integer()->unsigned()->null(),
                'updated_at' => $this->dateTime(),
            ]);

            $this->addColumn(Location::tableName(), 'tag_ids', $this->json()
                ->null()
                ->after('provider_id'));

            $this->addColumn(Location::tableName(), 'tag_count', $this->integer()
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->after('tag_ids'));
        });

        $auth = Yii::$app->getAuthManager();
        $admin = $auth->getRole(User::AUTH_ROLE_ADMIN);

        $tagUpdate = $auth->createPermission(Tag::AUTH_TAG_UPDATE);
        $tagUpdate->description = Yii::t('location', 'Update location tags', [], Yii::$app->sourceLanguage);
        $auth->add($tagUpdate);

        $auth->addChild($admin, $tagUpdate);

        $tagCreate = $auth->createPermission(Tag::AUTH_TAG_CREATE);
        $tagCreate->description = Yii::t('location', 'Create location tags', [], Yii::$app->sourceLanguage);
        $auth->add($tagCreate);

        $auth->addChild($admin, $tagCreate);
        $auth->addChild($tagUpdate, $tagCreate);

        $tagDelete = $auth->createPermission(Tag::AUTH_TAG_DELETE);
        $tagDelete->description = Yii::t('location', 'Delete location tags', [], Yii::$app->sourceLanguage);
        $auth->add($tagDelete);

        $auth->addChild($admin, $tagDelete);
        $auth->addChild($tagUpdate, $tagDelete);
    }

    public function safeDown(): void
    {
        $auth = Yii::$app->getAuthManager();

        $this->delete($auth->itemTable, ['name' => Tag::AUTH_TAG_DELETE]);
        $this->delete($auth->itemTable, ['name' => Tag::AUTH_TAG_CREATE]);
        $this->delete($auth->itemTable, ['name' => Tag::AUTH_TAG_UPDATE]);

        $this->i18nTablesCallback(function () {
            $this->dropColumn(Location::tableName(), 'tag_ids');
            $this->dropColumn(Location::tableName(), 'tag_count');

            $this->dropTable(LocationTag::tableName());
            $this->dropTable(Tag::tableName());
        });
    }
}