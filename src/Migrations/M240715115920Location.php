<?php

declare(strict_types=1);

namespace Hirtz\Location\Migrations;

use Hirtz\Location\Models\Location;
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

        foreach (['name', 'formatted_address'] as $attributeName) {
            $this->createIndex($attributeName, Location::tableName(), [$attributeName, 'status', 'type']);
        }

        $auth = Yii::$app->getAuthManager();
        $admin = $auth->getRole(User::AUTH_ROLE_ADMIN);

        $locationUpdate = $auth->createPermission('locationUpdate');
        $locationUpdate->description = 'Update locations';
        $auth->add($locationUpdate);

        $auth->addChild($admin, $locationUpdate);

        $locationCreate = $auth->createPermission('locationCreate');
        $locationCreate->description = 'Create locations';
        $auth->add($locationCreate);

        $auth->addChild($admin, $locationCreate);
        $auth->addChild($locationUpdate, $locationCreate);

        $locationDelete = $auth->createPermission('locationDelete');
        $locationDelete->description = 'Delete locations';
        $auth->add($locationDelete);

        $auth->addChild($admin, $locationDelete);
        $auth->addChild($locationUpdate, $locationDelete);
    }

    public function safeDown(): void
    {
        $auth = Yii::$app->getAuthManager();
        $this->delete($auth->itemTable, ['name' => 'locationDelete']);
        $this->delete($auth->itemTable, ['name' => 'locationCreate']);
        $this->delete($auth->itemTable, ['name' => 'locationUpdate']);

        $this->dropTable(Location::tableName());
    }
}
