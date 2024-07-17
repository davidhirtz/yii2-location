<?php

namespace davidhirtz\yii2\location\migrations;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\skeleton\db\traits\MigrationTrait;
use davidhirtz\yii2\skeleton\models\User;
use Yii;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */
class M240715115920Location extends Migration
{
    use MigrationTrait;

    public function safeUp(): void
    {
        $this->createTable(Location::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'status' => $this->smallInteger()->notNull()->defaultValue(Location::STATUS_DEFAULT),
            'type' => $this->smallInteger()->notNull()->defaultValue(Location::TYPE_DEFAULT),
            'name' => $this->string()->null(),
            'formatted_address' => $this->string()->null(),
            'street' => $this->string()->null(),
            'house_number' => $this->string()->null(),
            'locality' => $this->string()->null(),
            'postal_code' => $this->string()->null(),
            'district' => $this->string()->null(),
            'state' => $this->string()->null(),
            'country_code' => $this->string(2)->null(),
            'lat' => $this->decimal(10, 8)->null(),
            'lng' => $this->decimal(11, 8)->null(),
            'provider_id' => $this->text()->null(),
            'updated_by_user_id' => $this->integer()->unsigned()->null(),
            'updated_at' => $this->dateTime(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('name', Location::tableName(), ['name', 'status', 'type']);
        $this->createIndex('formatted_address', Location::tableName(), ['formatted_address', 'status', 'type']);

        $auth = Yii::$app->getAuthManager();
        $admin = $auth->getRole(User::AUTH_ROLE_ADMIN);

        $locationUpdate = $auth->createPermission(Location::AUTH_LOCATION_UPDATE);
        $locationUpdate->description = Yii::t('location', 'Update locations', [], Yii::$app->sourceLanguage);
        $auth->add($locationUpdate);

        $auth->addChild($admin, $locationUpdate);

        $locationCreate = $auth->createPermission(Location::AUTH_LOCATION_CREATE);
        $locationCreate->description = Yii::t('location', 'Create locations', [], Yii::$app->sourceLanguage);
        $auth->add($locationCreate);

        $auth->addChild($admin, $locationCreate);
        $auth->addChild($locationUpdate, $locationCreate);

        $locationDelete = $auth->createPermission(Location::AUTH_LOCATION_DELETE);
        $locationDelete->description = Yii::t('location', 'Delete locations', [], Yii::$app->sourceLanguage);
        $auth->add($locationDelete);

        $auth->addChild($admin, $locationDelete);
        $auth->addChild($locationUpdate, $locationDelete);
    }

    public function safeDown(): void
    {
        $this->dropTable(Location::tableName());

        $auth = Yii::$app->getAuthManager();
        $this->delete($auth->itemTable, ['name' => Location::AUTH_LOCATION_DELETE]);
        $this->delete($auth->itemTable, ['name' => Location::AUTH_LOCATION_CREATE]);
        $this->delete($auth->itemTable, ['name' => Location::AUTH_LOCATION_UPDATE]);
    }
}
