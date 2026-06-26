<?php

declare(strict_types=1);

namespace SP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial sysPass database schema migration
 *
 * This migration establishes the baseline schema for sysPass v3.1.x / v4.x.
 * It creates all required tables if they don't exist.
 *
 * For existing installations:
 *   Run: php bin/console migrations:sync-metadata-storage
 *   Then: php bin/console migrations:version --add 'SP\Migrations\Version20260116000000'
 *
 * For new installations:
 *   Run: php bin/console migrations:migrate
 *
 * This corresponds to database version 310.19042701 (the latest in UpgradeDatabaseService::UPGRADES)
 */
final class Version20260116000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial sysPass database schema (baseline for v3.1.x/v4.x)';
    }

    public function up(Schema $schema): void
    {
        // Check if tables already exist (existing installation)
        $tableExists = $this->connection->executeQuery(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'Account'"
        )->fetchOne();

        if ($tableExists > 0) {
            $this->write('  <comment>Tables already exist. Skipping schema creation.</comment>');
            return;
        }

        // Create tables in dependency order (tables with no FKs first)
        $this->createUserGroupTable($schema);
        $this->createUserProfileTable($schema);
        $this->createCategoryTable($schema);
        $this->createClientTable($schema);
        $this->createTagTable($schema);
        $this->createConfigTable($schema);
        $this->createCustomFieldTypeTable($schema);
        $this->createPluginTable($schema);

        // Tables with foreign keys
        $this->createUserTable($schema);
        $this->createAccountTable($schema);
        $this->createAccountHistoryTable($schema);
        $this->createAccountFileTable($schema);
        $this->createAccountToFavoriteTable($schema);
        $this->createAccountToTagTable($schema);
        $this->createAccountToUserTable($schema);
        $this->createAccountToUserGroupTable($schema);
        $this->createAuthTokenTable($schema);
        $this->createCustomFieldDefinitionTable($schema);
        $this->createCustomFieldDataTable($schema);
        $this->createEventLogTable($schema);
        $this->createItemPresetTable($schema);
        $this->createNotificationTable($schema);
        $this->createPluginDataTable($schema);
        $this->createPublicLinkTable($schema);
        $this->createTrackTable($schema);
        $this->createUserPassRecoverTable($schema);
        $this->createUserToUserGroupTable($schema);

        // Insert default data
        $this->insertDefaultData();

        // Create views
        $this->createViews();
    }

    public function down(Schema $schema): void
    {
        // Drop views first
        $this->addSql('DROP VIEW IF EXISTS account_search_v');
        $this->addSql('DROP VIEW IF EXISTS account_data_v');

        // Drop tables in reverse dependency order
        $tables = [
            'UserToUserGroup', 'UserPassRecover', 'Track', 'PublicLink', 'PluginData',
            'Notification', 'ItemPreset', 'EventLog', 'CustomFieldData', 'CustomFieldDefinition',
            'AuthToken', 'AccountToUserGroup', 'AccountToUser', 'AccountToTag', 'AccountToFavorite',
            'AccountFile', 'AccountHistory', 'Account', 'User', 'Plugin', 'CustomFieldType',
            'Config', 'Tag', 'Client', 'Category', 'UserProfile', 'UserGroup'
        ];

        foreach ($tables as $table) {
            $this->addSql("DROP TABLE IF EXISTS `$table`");
        }
    }

    private function createUserGroupTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `UserGroup` (
                `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `description` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createUserProfileTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `UserProfile` (
                `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(45) NOT NULL,
                `profile` blob NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createCategoryTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Category` (
                `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `description` varchar(255) DEFAULT NULL,
                `hash` varbinary(40) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_Category_01` (`hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createClientTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Client` (
                `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `hash` varbinary(40) NOT NULL,
                `description` varchar(255) DEFAULT NULL,
                `isGlobal` tinyint(1) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `uk_Client_01` (`hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createTagTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Tag` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(45) NOT NULL,
                `hash` varbinary(40) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_Tag_01` (`hash`),
                KEY `idx_Tag_01` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createConfigTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Config` (
                `parameter` varchar(50) NOT NULL,
                `value` varchar(4000) DEFAULT NULL,
                PRIMARY KEY (`parameter`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createCustomFieldTypeTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `CustomFieldType` (
                `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `text` varchar(50) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_CustomFieldType_01` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createPluginTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Plugin` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `data` mediumblob DEFAULT NULL,
                `enabled` tinyint(1) NOT NULL DEFAULT 0,
                `available` tinyint(1) DEFAULT 0,
                `versionLevel` varchar(15) NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_Plugin_01` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createUserTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `User` (
                `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(80) NOT NULL,
                `userGroupId` smallint(5) unsigned NOT NULL,
                `login` varchar(50) NOT NULL,
                `ssoLogin` varchar(100) DEFAULT NULL,
                `pass` varbinary(500) NOT NULL,
                `mPass` varbinary(2000) DEFAULT NULL,
                `mKey` varbinary(2000) DEFAULT NULL,
                `email` varchar(80) DEFAULT NULL,
                `notes` text DEFAULT NULL,
                `loginCount` int(10) unsigned NOT NULL DEFAULT 0,
                `userProfileId` smallint(5) unsigned NOT NULL,
                `lastLogin` datetime DEFAULT NULL,
                `lastUpdate` datetime DEFAULT NULL,
                `lastUpdateMPass` int(11) unsigned NOT NULL DEFAULT 0,
                `isAdminApp` tinyint(1) DEFAULT 0,
                `isAdminAcc` tinyint(1) DEFAULT 0,
                `isLdap` tinyint(1) DEFAULT 0,
                `isDisabled` tinyint(1) DEFAULT 0,
                `hashSalt` varbinary(255) NOT NULL,
                `isMigrate` tinyint(1) DEFAULT 0,
                `isChangePass` tinyint(1) DEFAULT 0,
                `isChangedPass` tinyint(1) DEFAULT 0,
                `preferences` blob DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_User_01` (`login`, `ssoLogin`),
                KEY `idx_User_01` (`pass`),
                KEY `fk_User_userGroupId` (`userGroupId`),
                KEY `fk_User_userProfileId` (`userProfileId`),
                CONSTRAINT `fk_User_userGroupId` FOREIGN KEY (`userGroupId`) REFERENCES `UserGroup` (`id`),
                CONSTRAINT `fk_User_userProfileId` FOREIGN KEY (`userProfileId`) REFERENCES `UserProfile` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAccountTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Account` (
                `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                `userGroupId` smallint(5) unsigned NOT NULL,
                `userId` smallint(5) unsigned NOT NULL,
                `userEditId` smallint(5) unsigned NOT NULL,
                `clientId` mediumint(8) unsigned NOT NULL,
                `name` varchar(100) NOT NULL,
                `categoryId` mediumint(8) unsigned NOT NULL,
                `login` varchar(50) DEFAULT NULL,
                `url` varchar(255) DEFAULT NULL,
                `pass` varbinary(2000) NOT NULL,
                `key` varbinary(2000) NOT NULL,
                `notes` text DEFAULT NULL,
                `countView` int(10) unsigned NOT NULL DEFAULT 0,
                `countDecrypt` int(10) unsigned NOT NULL DEFAULT 0,
                `dateAdd` datetime NOT NULL,
                `dateEdit` datetime DEFAULT NULL,
                `otherUserGroupEdit` tinyint(1) DEFAULT 0,
                `otherUserEdit` tinyint(1) DEFAULT 0,
                `isPrivate` tinyint(1) DEFAULT 0,
                `isPrivateGroup` tinyint(1) DEFAULT 0,
                `passDate` int(11) unsigned DEFAULT NULL,
                `passDateChange` int(11) unsigned DEFAULT NULL,
                `parentId` mediumint(8) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_Account_01` (`categoryId`),
                KEY `idx_Account_02` (`userGroupId`, `userId`),
                KEY `idx_Account_03` (`clientId`),
                KEY `idx_Account_04` (`parentId`),
                KEY `fk_Account_userId` (`userId`),
                KEY `fk_Account_userEditId` (`userEditId`),
                CONSTRAINT `fk_Account_categoryId` FOREIGN KEY (`categoryId`) REFERENCES `Category` (`id`),
                CONSTRAINT `fk_Account_clientId` FOREIGN KEY (`clientId`) REFERENCES `Client` (`id`),
                CONSTRAINT `fk_Account_userEditId` FOREIGN KEY (`userEditId`) REFERENCES `User` (`id`),
                CONSTRAINT `fk_Account_userGroupId` FOREIGN KEY (`userGroupId`) REFERENCES `UserGroup` (`id`),
                CONSTRAINT `fk_Account_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAccountHistoryTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `AccountHistory` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `accountId` mediumint(8) unsigned NOT NULL,
                `userGroupId` smallint(5) unsigned NOT NULL,
                `userId` smallint(5) unsigned NOT NULL,
                `userEditId` smallint(5) unsigned NOT NULL,
                `clientId` mediumint(8) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `categoryId` mediumint(8) unsigned NOT NULL,
                `login` varchar(50) DEFAULT NULL,
                `url` varchar(255) DEFAULT NULL,
                `pass` varbinary(2000) NOT NULL,
                `key` varbinary(2000) NOT NULL,
                `notes` text NOT NULL,
                `countView` int(10) unsigned NOT NULL DEFAULT 0,
                `countDecrypt` int(10) unsigned NOT NULL DEFAULT 0,
                `dateAdd` datetime NOT NULL,
                `dateEdit` datetime DEFAULT NULL,
                `isModify` tinyint(1) DEFAULT 0,
                `isDeleted` tinyint(1) DEFAULT 0,
                `mPassHash` varbinary(255) NOT NULL,
                `otherUserEdit` tinyint(1) DEFAULT 0,
                `otherUserGroupEdit` tinyint(1) DEFAULT 0,
                `passDate` int(10) unsigned DEFAULT NULL,
                `passDateChange` int(10) unsigned DEFAULT NULL,
                `parentId` mediumint(8) unsigned DEFAULT NULL,
                `isPrivate` tinyint(1) DEFAULT 0,
                `isPrivateGroup` tinyint(1) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_AccountHistory_01` (`accountId`),
                KEY `idx_AccountHistory_02` (`parentId`),
                KEY `fk_AccountHistory_userGroupId` (`userGroupId`),
                KEY `fk_AccountHistory_userId` (`userId`),
                KEY `fk_AccountHistory_userEditId` (`userEditId`),
                KEY `fk_AccountHistory_clientId` (`clientId`),
                KEY `fk_AccountHistory_categoryId` (`categoryId`),
                CONSTRAINT `fk_AccountHistory_categoryId` FOREIGN KEY (`categoryId`) REFERENCES `Category` (`id`),
                CONSTRAINT `fk_AccountHistory_clientId` FOREIGN KEY (`clientId`) REFERENCES `Client` (`id`),
                CONSTRAINT `fk_AccountHistory_userEditId` FOREIGN KEY (`userEditId`) REFERENCES `User` (`id`),
                CONSTRAINT `fk_AccountHistory_userGroupId` FOREIGN KEY (`userGroupId`) REFERENCES `UserGroup` (`id`),
                CONSTRAINT `fk_AccountHistory_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAccountFileTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `AccountFile` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `accountId` mediumint(5) unsigned NOT NULL,
                `name` varchar(100) NOT NULL,
                `type` varchar(100) NOT NULL,
                `size` int(11) NOT NULL,
                `content` mediumblob NOT NULL,
                `extension` varchar(10) NOT NULL,
                `thumb` mediumblob DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_AccountFile_01` (`accountId`),
                CONSTRAINT `fk_AccountFile_accountId` FOREIGN KEY (`accountId`) REFERENCES `Account` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAccountToFavoriteTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `AccountToFavorite` (
                `accountId` mediumint(8) unsigned NOT NULL,
                `userId` smallint(5) unsigned NOT NULL,
                PRIMARY KEY (`accountId`, `userId`),
                KEY `idx_AccountToFavorite_01` (`accountId`, `userId`),
                KEY `fk_AccountToFavorite_userId` (`userId`),
                CONSTRAINT `fk_AccountToFavorite_accountId` FOREIGN KEY (`accountId`) REFERENCES `Account` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_AccountToFavorite_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAccountToTagTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `AccountToTag` (
                `accountId` mediumint(8) unsigned NOT NULL,
                `tagId` int(10) unsigned NOT NULL,
                PRIMARY KEY (`accountId`, `tagId`),
                KEY `fk_AccountToTag_accountId` (`accountId`),
                KEY `fk_AccountToTag_tagId` (`tagId`),
                CONSTRAINT `fk_AccountToTag_accountId` FOREIGN KEY (`accountId`) REFERENCES `Account` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_AccountToTag_tagId` FOREIGN KEY (`tagId`) REFERENCES `Tag` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAccountToUserTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `AccountToUser` (
                `accountId` mediumint(8) unsigned NOT NULL,
                `userId` smallint(5) unsigned NOT NULL,
                `isEdit` tinyint(1) unsigned DEFAULT 0 NULL,
                PRIMARY KEY (`accountId`, `userId`),
                KEY `idx_AccountToUser_01` (`accountId`),
                KEY `fk_AccountToUser_userId` (`userId`),
                CONSTRAINT `fk_AccountToUser_accountId` FOREIGN KEY (`accountId`) REFERENCES `Account` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_AccountToUser_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAccountToUserGroupTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `AccountToUserGroup` (
                `accountId` mediumint(8) unsigned NOT NULL,
                `userGroupId` smallint(5) unsigned NOT NULL,
                `isEdit` tinyint(1) unsigned DEFAULT 0 NULL,
                PRIMARY KEY (`accountId`, `userGroupId`),
                KEY `idx_AccountToUserGroup_01` (`accountId`),
                KEY `fk_AccountToUserGroup_userGroupId` (`userGroupId`),
                CONSTRAINT `fk_AccountToUserGroup_accountId` FOREIGN KEY (`accountId`) REFERENCES `Account` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_AccountToUserGroup_userGroupId` FOREIGN KEY (`userGroupId`) REFERENCES `UserGroup` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createAuthTokenTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `AuthToken` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `userId` smallint(5) unsigned NOT NULL,
                `token` varbinary(255) NOT NULL,
                `actionId` smallint(5) unsigned NOT NULL,
                `createdBy` smallint(5) unsigned NOT NULL,
                `startDate` int(10) unsigned NOT NULL,
                `vault` varbinary(2000) DEFAULT NULL,
                `hash` varbinary(500) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_AuthToken_01` (`token`, `actionId`),
                KEY `idx_AuthToken_01` (`userId`, `actionId`, `token`),
                KEY `fk_AuthToken_actionId` (`actionId`),
                CONSTRAINT `fk_AuthToken_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createCustomFieldDefinitionTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `CustomFieldDefinition` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `moduleId` smallint(5) unsigned NOT NULL,
                `required` tinyint(1) unsigned DEFAULT NULL,
                `help` varchar(255) DEFAULT NULL,
                `showInList` tinyint(1) unsigned DEFAULT NULL,
                `typeId` tinyint(3) unsigned NOT NULL,
                `isEncrypted` tinyint(1) unsigned DEFAULT 1 NULL,
                PRIMARY KEY (`id`),
                KEY `fk_CustomFieldDefinition_typeId` (`typeId`),
                CONSTRAINT `fk_CustomFieldDefinition_typeId` FOREIGN KEY (`typeId`) REFERENCES `CustomFieldType` (`id`)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createCustomFieldDataTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `CustomFieldData` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `moduleId` smallint(5) unsigned NOT NULL,
                `itemId` int(10) unsigned NOT NULL,
                `definitionId` int(10) unsigned NOT NULL,
                `data` longblob DEFAULT NULL,
                `key` varbinary(2000) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_CustomFieldData_01` (`definitionId`),
                KEY `idx_CustomFieldData_02` (`itemId`, `moduleId`),
                KEY `idx_CustomFieldData_03` (`moduleId`),
                KEY `uk_CustomFieldData_01` (`moduleId`, `itemId`, `definitionId`),
                CONSTRAINT `fk_CustomFieldData_definitionId` FOREIGN KEY (`definitionId`) REFERENCES `CustomFieldDefinition` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createEventLogTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `EventLog` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `date` int(10) unsigned NOT NULL,
                `login` varchar(25) DEFAULT NULL,
                `userId` smallint(5) unsigned DEFAULT NULL,
                `ipAddress` varchar(45) NOT NULL,
                `action` varchar(50) NOT NULL,
                `description` text DEFAULT NULL,
                `level` varchar(20) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createItemPresetTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `ItemPreset` (
                `id` int NOT NULL AUTO_INCREMENT,
                `type` varchar(25) NOT NULL,
                `userId` smallint(5) unsigned,
                `userGroupId` smallint(5) unsigned,
                `userProfileId` smallint(5) unsigned,
                `fixed` tinyint(1) unsigned DEFAULT 0 NOT NULL,
                `priority` tinyint(3) unsigned DEFAULT 0 NOT NULL,
                `data` blob,
                `hash` varbinary(40) NOT NULL,
                UNIQUE INDEX `uk_ItemPreset_01` (`hash`),
                CONSTRAINT `fk_ItemPreset_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_ItemPreset_userGroupId` FOREIGN KEY (`userGroupId`) REFERENCES `UserGroup` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_ItemPreset_userProfileId` FOREIGN KEY (`userProfileId`) REFERENCES `UserProfile` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createNotificationTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Notification` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `type` varchar(100) DEFAULT NULL,
                `component` varchar(100) NOT NULL,
                `description` text NOT NULL,
                `date` int(10) unsigned NOT NULL,
                `checked` tinyint(1) DEFAULT 0,
                `userId` smallint(5) unsigned DEFAULT NULL,
                `sticky` tinyint(1) DEFAULT 0,
                `onlyAdmin` tinyint(1) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_Notification_01` (`userId`, `checked`, `date`),
                KEY `idx_Notification_02` (`component`, `date`, `checked`, `userId`),
                KEY `fk_Notification_userId` (`userId`),
                CONSTRAINT `fk_Notification_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createPluginDataTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `PluginData` (
                `name` varchar(100) NOT NULL,
                `itemId` int NOT NULL,
                `data` blob NOT NULL,
                `key` varbinary(2000) NOT NULL,
                PRIMARY KEY (`name`, `itemId`),
                CONSTRAINT `fk_PluginData_name` FOREIGN KEY (`name`) REFERENCES `Plugin` (`name`)
                    ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createPublicLinkTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `PublicLink` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `itemId` int(10) unsigned NOT NULL,
                `hash` varbinary(100) NOT NULL,
                `data` mediumblob DEFAULT NULL,
                `userId` smallint(5) unsigned NOT NULL,
                `typeId` int(10) unsigned NOT NULL,
                `notify` tinyint(1) DEFAULT 0,
                `dateAdd` int(10) unsigned NOT NULL,
                `dateExpire` int(10) unsigned NOT NULL,
                `dateUpdate` int(10) unsigned DEFAULT 0,
                `countViews` smallint(5) unsigned DEFAULT 0,
                `totalCountViews` mediumint(8) unsigned DEFAULT 0,
                `maxCountViews` smallint(5) unsigned NOT NULL DEFAULT 0,
                `useinfo` blob DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_PublicLink_01` (`hash`),
                UNIQUE KEY `uk_PublicLink_02` (`itemId`),
                KEY `fk_PublicLink_userId` (`userId`),
                CONSTRAINT `fk_PublicLink_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createTrackTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `Track` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `userId` smallint(5) unsigned DEFAULT NULL,
                `source` varchar(100) NOT NULL,
                `time` int(10) unsigned NOT NULL,
                `timeUnlock` int(10) unsigned,
                `ipv4` binary(4) DEFAULT NULL,
                `ipv6` binary(16) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_Track_01` (`userId`),
                KEY `idx_Track_02` (`time`, `ipv4`, `ipv6`, `source`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createUserPassRecoverTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `UserPassRecover` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `userId` smallint(5) unsigned NOT NULL,
                `hash` varbinary(255) NOT NULL,
                `date` int(10) unsigned NOT NULL,
                `used` tinyint(1) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_UserPassRecover_01` (`userId`, `date`),
                CONSTRAINT `fk_UserPassRecover_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function createUserToUserGroupTable(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `UserToUserGroup` (
                `userId` smallint(5) unsigned NOT NULL,
                `userGroupId` smallint(5) unsigned NOT NULL,
                KEY `idx_UserToUserGroup_01` (`userId`),
                KEY `fk_UserToGroup_userGroupId` (`userGroupId`),
                UNIQUE KEY `uk_UserToUserGroup_01` (`userId`, `userGroupId`),
                CONSTRAINT `fk_UserToGroup_userGroupId` FOREIGN KEY (`userGroupId`) REFERENCES `UserGroup` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_UserToGroup_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    private function insertDefaultData(): void
    {
        $this->addSql("
            INSERT INTO CustomFieldType (id, name, text) VALUES
            (1, 'text', 'Text'),
            (2, 'password', 'Password'),
            (3, 'date', 'Date'),
            (4, 'number', 'Number'),
            (5, 'email', 'Email'),
            (6, 'telephone', 'Phone'),
            (7, 'url', 'URL'),
            (8, 'color', 'Color'),
            (9, 'wiki', 'Wiki'),
            (10, 'textarea', 'Text Area')
        ");
    }

    private function createViews(): void
    {
        $this->addSql("
            CREATE VIEW `account_data_v` AS
            SELECT `Account`.`id` AS `id`,
                   `Account`.`name` AS `name`,
                   `Account`.`categoryId` AS `categoryId`,
                   `Account`.`userId` AS `userId`,
                   `Account`.`clientId` AS `clientId`,
                   `Account`.`userGroupId` AS `userGroupId`,
                   `Account`.`userEditId` AS `userEditId`,
                   `Account`.`login` AS `login`,
                   `Account`.`url` AS `url`,
                   `Account`.`notes` AS `notes`,
                   `Account`.`countView` AS `countView`,
                   `Account`.`countDecrypt` AS `countDecrypt`,
                   `Account`.`dateAdd` AS `dateAdd`,
                   `Account`.`dateEdit` AS `dateEdit`,
                   CONV(`Account`.`otherUserEdit`, 10, 2) AS `otherUserEdit`,
                   CONV(`Account`.`otherUserGroupEdit`, 10, 2) AS `otherUserGroupEdit`,
                   CONV(`Account`.`isPrivate`, 10, 2) AS `isPrivate`,
                   CONV(`Account`.`isPrivateGroup`, 10, 2) AS `isPrivateGroup`,
                   `Account`.`passDate` AS `passDate`,
                   `Account`.`passDateChange` AS `passDateChange`,
                   `Account`.`parentId` AS `parentId`,
                   `Category`.`name` AS `categoryName`,
                   `Client`.`name` AS `clientName`,
                   `ug`.`name` AS `userGroupName`,
                   `u1`.`name` AS `userName`,
                   `u1`.`login` AS `userLogin`,
                   `u2`.`name` AS `userEditName`,
                   `u2`.`login` AS `userEditLogin`,
                   `PublicLink`.`hash` AS `publicLinkHash`
            FROM ((((((`Account`
            LEFT JOIN `Category` ON (`Account`.`categoryId` = `Category`.`id`))
            JOIN `UserGroup` `ug` ON (`Account`.`userGroupId` = `ug`.`id`))
            JOIN `User` `u1` ON (`Account`.`userId` = `u1`.`id`))
            JOIN `User` `u2` ON (`Account`.`userEditId` = `u2`.`id`))
            LEFT JOIN `Client` ON (`Account`.`clientId` = `Client`.`id`))
            LEFT JOIN `PublicLink` ON (`Account`.`id` = `PublicLink`.`itemId`))
        ");

        $this->addSql("
            CREATE VIEW `account_search_v` AS
            SELECT `Account`.`id` AS `id`,
                   `Account`.`clientId` AS `clientId`,
                   `Account`.`categoryId` AS `categoryId`,
                   `Account`.`name` AS `name`,
                   `Account`.`login` AS `login`,
                   `Account`.`url` AS `url`,
                   `Account`.`notes` AS `notes`,
                   `Account`.`userId` AS `userId`,
                   `Account`.`userGroupId` AS `userGroupId`,
                   `Account`.`otherUserEdit` AS `otherUserEdit`,
                   `Account`.`otherUserGroupEdit` AS `otherUserGroupEdit`,
                   `Account`.`isPrivate` AS `isPrivate`,
                   `Account`.`isPrivateGroup` AS `isPrivateGroup`,
                   `Account`.`passDate` AS `passDate`,
                   `Account`.`passDateChange` AS `passDateChange`,
                   `Account`.`parentId` AS `parentId`,
                   `Account`.`countView` AS `countView`,
                   `Account`.`dateEdit` AS `dateEdit`,
                   `User`.`name` AS `userName`,
                   `User`.`login` AS `userLogin`,
                   `UserGroup`.`name` AS `userGroupName`,
                   `Category`.`name` AS `categoryName`,
                   `Client`.`name` AS `clientName`,
                   (SELECT COUNT(0) FROM `AccountFile` WHERE (`AccountFile`.`accountId` = `Account`.`id`)) AS `num_files`,
                   `PublicLink`.`hash` AS `publicLinkHash`,
                   `PublicLink`.`dateExpire` AS `publicLinkDateExpire`,
                   `PublicLink`.`totalCountViews` AS `publicLinkTotalCountViews`
            FROM `Account`
            INNER JOIN `Category` ON `Account`.`categoryId` = `Category`.`id`
            INNER JOIN `Client` ON `Client`.`id` = `Account`.`clientId`
            INNER JOIN `User` ON `Account`.`userId` = `User`.`id`
            INNER JOIN `UserGroup` ON `Account`.`userGroupId` = `UserGroup`.`id`
            LEFT JOIN `PublicLink` ON `Account`.`id` = `PublicLink`.`itemId`
        ");
    }
}
