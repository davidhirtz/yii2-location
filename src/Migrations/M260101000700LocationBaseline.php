<?php

declare(strict_types=1);

namespace Hirtz\Location\Migrations;

use Override;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */
class M260101000700LocationBaseline extends Migration
{
    #[Override]
    public function safeUp(): void
    {
        $this->execute(
            <<<'SQL'
            CREATE TABLE `location` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `status` smallint(6) NOT NULL DEFAULT 3,
              `type` smallint(6) NOT NULL DEFAULT 1,
              `name` varchar(255) DEFAULT NULL,
              `formatted_address` varchar(255) DEFAULT NULL,
              `street` varchar(255) DEFAULT NULL,
              `house_number` varchar(255) DEFAULT NULL,
              `locality` varchar(255) DEFAULT NULL,
              `postal_code` varchar(255) DEFAULT NULL,
              `district` varchar(255) DEFAULT NULL,
              `state` varchar(255) DEFAULT NULL,
              `country_code` varchar(2) DEFAULT NULL,
              `lat` decimal(10,8) DEFAULT NULL,
              `lng` decimal(11,8) DEFAULT NULL,
              `provider_id` text DEFAULT NULL,
              `tag_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tag_ids`)),
              `tag_count` int(11) unsigned NOT NULL DEFAULT 0,
              `updated_by_user_id` int(11) unsigned DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `created_at` datetime NOT NULL,
              `custom_attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_attributes`)),
              PRIMARY KEY (`id`),
              KEY `name` (`name`,`status`,`type`),
              KEY `formatted_address` (`formatted_address`,`status`,`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL
        );

        $this->execute(
            <<<'SQL'
            CREATE TABLE `tag` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `status` smallint(6) NOT NULL DEFAULT 3,
              `type` smallint(6) NOT NULL DEFAULT 1,
              `name` varchar(255) NOT NULL,
              `location_count` int(11) unsigned NOT NULL DEFAULT 0,
              `updated_by_user_id` int(11) unsigned DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `created_at` datetime NOT NULL,
              `custom_attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_attributes`)),
              PRIMARY KEY (`id`),
              UNIQUE KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL
        );

        $this->execute(
            <<<'SQL'
            CREATE TABLE `location_tag` (
              `location_id` int(11) unsigned NOT NULL,
              `tag_id` int(11) unsigned NOT NULL,
              `updated_by_user_id` int(11) unsigned DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`location_id`,`tag_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL
        );

        $this->execute(
            <<<'SQL'
            INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `updated_at`, `created_at`) VALUES
              ('location', '2', '{\"category\":\"location\",\"key\":\"AUTH_LOCATION_DESCRIPTION\"}', NULL, NULL, '1789985581', '1789985581'),
              ('tag', '2', '{\"category\":\"location\",\"key\":\"AUTH_TAG_DESCRIPTION\"}', NULL, NULL, '1789985581', '1789985581')
            SQL
        );

        $this->execute(
            <<<'SQL'
            INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
              ('admin', 'location'),
              ('manager', 'location'),
              ('admin', 'tag'),
              ('manager', 'tag')
            SQL
        );
    }

    #[Override]
    public function safeDown(): bool
    {
        echo "    > a baseline cannot be reverted, restore a dump instead\n";
        return false;
    }
}
