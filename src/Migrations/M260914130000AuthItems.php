<?php

declare(strict_types=1);

namespace Hirtz\Location\Migrations;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Skeleton\Db\Traits\MigrationTrait;
use Hirtz\Skeleton\I18n\Message;
use Hirtz\Skeleton\Models\User;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */
class M260914130000AuthItems extends Migration
{
    use MigrationTrait;

    private const array LEGACY_LOCATION = ['locationCreate', 'locationDelete', 'locationUpdate'];
    private const array LEGACY_TAG = ['tagCreate', 'tagDelete', 'tagUpdate'];

    public function safeUp(): void
    {
        $this->addPermission(Location::AUTH_LOCATION, $this->getLocationDescription(), User::AUTH_ROLE_ADMIN);
        $this->replaceAuthItems(self::LEGACY_LOCATION, Location::AUTH_LOCATION);

        $this->addPermission(Tag::AUTH_TAG, $this->getTagDescription(), User::AUTH_ROLE_ADMIN);
        $this->replaceAuthItems(self::LEGACY_TAG, Tag::AUTH_TAG);
    }

    public function safeDown(): void
    {
        $this->restoreAuthItems(self::LEGACY_TAG, Tag::AUTH_TAG, $this->getTagDescription());
        $this->restoreAuthItems(self::LEGACY_LOCATION, Location::AUTH_LOCATION, $this->getLocationDescription());
    }

    private function getLocationDescription(): Message
    {
        return Message::make('location', 'AUTH_LOCATION_DESCRIPTION');
    }

    private function getTagDescription(): Message
    {
        return Message::make('location', 'AUTH_TAG_DESCRIPTION');
    }
}
