#!/usr/bin/env php
<?php
/**
 * Migration script from old sysPass schema to new schema
 * Migrates data from syspass_dump (v2.x) to syspass (v3.x)
 */

declare(strict_types=1);

echo "=== sysPass Database Migration Tool ===\n";
echo "Migrating from: syspass_dump (old schema)\n";
echo "Migrating to: syspass (new schema)\n\n";

$oldDb = new mysqli('localhost', 'syspass', 'syspass', 'syspass_dump');
$newDb = new mysqli('localhost', 'syspass', 'syspass', 'syspass');

if ($oldDb->connect_error) {
    die("Old DB connection failed: " . $oldDb->connect_error . "\n");
}
if ($newDb->connect_error) {
    die("New DB connection failed: " . $newDb->connect_error . "\n");
}

echo "✓ Connected to both databases\n\n";

// Mapping of old table names to new ones
$tableMappings = [
    'categories' => 'Category',
    'customers' => 'Client',
    'usrData' => 'User',
    'usrGroups' => 'UserGroup',
    'usrProfiles' => 'UserProfile',
    'tags' => 'Tag',
];

$migrationStats = [];

try {
    $newDb->begin_transaction();

    // 1. Migrate Categories
    echo "[1/7] Migrating Categories...\n";
    $result = $oldDb->query("SELECT * FROM categories");
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $stmt = $newDb->prepare("
            INSERT INTO Category (id, name, description, hash)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description)
        ");
        $stmt->bind_param('isss',
            $row['category_id'],
            $row['category_name'],
            $row['category_description'] ?? '',
            md5($row['category_name'])
        );
        $stmt->execute();
        $count++;
    }
    $migrationStats['categories'] = $count;
    echo "  ✓ Migrated $count categories\n";

    // 2. Migrate Clients
    echo "[2/7] Migrating Clients...\n";
    $result = $oldDb->query("SELECT * FROM customers");
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $stmt = $newDb->prepare("
            INSERT INTO Client (id, name, description, hash, isGlobal)
            VALUES (?, ?, ?, ?, 0)
            ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description)
        ");
        $stmt->bind_param('isss',
            $row['customer_id'],
            $row['customer_name'],
            $row['customer_description'] ?? '',
            md5($row['customer_name'])
        );
        $stmt->execute();
        $count++;
    }
    $migrationStats['clients'] = $count;
    echo "  ✓ Migrated $count clients\n";

    // 3. Migrate Tags
    echo "[3/7] Migrating Tags...\n";
    $result = $oldDb->query("SELECT * FROM tags");
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $stmt = $newDb->prepare("
            INSERT INTO Tag (id, name, hash)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE name=VALUES(name)
        ");
        $stmt->bind_param('iss',
            $row['tag_id'],
            $row['tag_name'],
            md5($row['tag_name'])
        );
        $stmt->execute();
        $count++;
    }
    $migrationStats['tags'] = $count;
    echo "  ✓ Migrated $count tags\n";

    // 4. Migrate User Groups
    echo "[4/7] Migrating User Groups...\n";
    $result = $oldDb->query("SELECT * FROM usrGroups");
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $stmt = $newDb->prepare("
            INSERT INTO UserGroup (id, name, description)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description)
        ");
        $stmt->bind_param('iss',
            $row['usergroup_id'],
            $row['usergroup_name'],
            $row['usergroup_description'] ?? ''
        );
        $stmt->execute();
        $count++;
    }
    $migrationStats['usergroups'] = $count;
    echo "  ✓ Migrated $count user groups\n";

    // 5. Migrate Users (only if they don't exist)
    echo "[5/7] Migrating Users...\n";
    $result = $oldDb->query("SELECT * FROM usrData");
    $count = 0;
    $skipped = 0;
    while ($row = $result->fetch_assoc()) {
        // Check if user already exists
        $checkStmt = $newDb->prepare("SELECT id FROM User WHERE login = ?");
        $checkStmt->bind_param('s', $row['user_login']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $skipped++;
            continue;
        }

        $stmt = $newDb->prepare("
            INSERT INTO User (id, login, name, email, notes, groupId, profileId,
                             pass, hashSalt, isAdminApp, isAdminAcc, isDisabled,
                             isChangePass, isLdap, isMigrate, lastUpdate)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param('issssiissiiiii',
            $row['user_id'],
            $row['user_login'],
            $row['user_name'],
            $row['user_email'] ?? '',
            $row['user_notes'] ?? '',
            $row['user_groupId'] ?? 1,
            $row['user_profileId'] ?? 1,
            $row['user_pass'] ?? '',
            $row['user_hashSalt'] ?? '',
            $row['user_isAdminApp'] ?? 0,
            $row['user_isAdminAcc'] ?? 0,
            $row['user_isDisabled'] ?? 0,
            $row['user_isChangePass'] ?? 0,
            $row['user_isLdap'] ?? 0,
            $row['user_isMigrate'] ?? 0
        );
        $stmt->execute();
        $count++;
    }
    $migrationStats['users'] = $count;
    echo "  ✓ Migrated $count users ($skipped skipped - already exist)\n";

    // 6. Migrate Accounts (the most important part)
    echo "[6/7] Migrating Accounts...\n";
    $result = $oldDb->query("SELECT * FROM accounts");
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        // Note: Passwords are encrypted, we'll copy them as-is
        $stmt = $newDb->prepare("
            INSERT INTO Account (id, name, categoryId, clientId, login, url,
                                pass, `key`, notes, userId, userGroupId, userEditId,
                                dateAdd, dateEdit, passDate, passDateChange,
                                isPrivate, isPrivateGroup, parentId, countView, countDecrypt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    FROM_UNIXTIME(?), FROM_UNIXTIME(?), ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param('isiisssssiiiiiiiiiii',
            $row['account_id'],
            $row['account_name'],
            $row['account_categoryId'] ?? 1,
            $row['account_customerId'] ?? 1,
            $row['account_login'] ?? '',
            $row['account_url'] ?? '',
            $row['account_pass'] ?? '',
            $row['account_key'] ?? '',
            $row['account_notes'] ?? '',
            $row['account_userId'] ?? 1,
            $row['account_userGroupId'] ?? 1,
            $row['account_userEditId'] ?? 1,
            $row['account_dateAdd'] ?? time(),
            $row['account_dateEdit'] ?? time(),
            $row['account_passDate'] ?? time(),
            $row['account_passDateChange'] ?? 0,
            $row['account_otherUserEdit'] ?? 0,
            $row['account_otherGroupEdit'] ?? 0,
            $row['account_parentId'] ?? null,
            $row['account_countView'] ?? 0,
            $row['account_countDecrypt'] ?? 0
        );

        try {
            $stmt->execute();
            $count++;
        } catch (Exception $e) {
            echo "  ⚠ Warning: Could not migrate account '{$row['account_name']}': " . $e->getMessage() . "\n";
        }
    }
    $migrationStats['accounts'] = $count;
    echo "  ✓ Migrated $count accounts\n";

    // 7. Update account count in config
    echo "[7/7] Updating configuration...\n";
    $totalAccounts = $newDb->query("SELECT COUNT(*) as cnt FROM Account")->fetch_assoc()['cnt'];
    $newDb->query("UPDATE Config SET `value` = '$totalAccounts' WHERE `parameter` = 'account_count'");
    echo "  ✓ Updated account count to $totalAccounts\n";

    $newDb->commit();

    echo "\n=== Migration Complete ===\n";
    echo "Summary:\n";
    foreach ($migrationStats as $type => $count) {
        echo "  - $type: $count records\n";
    }
    echo "\nYou can now use the 'syspass' database with all migrated data!\n";

} catch (Exception $e) {
    $newDb->rollback();
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

$oldDb->close();
$newDb->close();
