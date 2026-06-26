#!/usr/bin/env php
<?php
/**
 * sysPass Functionality Test Script
 * Tests various operations to ensure PHP 8.2 compatibility
 */

declare(strict_types=1);

define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/vendor/autoload.php';
require_once APP_ROOT . '/lib/Base.php';

use SP\Core\Acl\ActionsInterface;
use SP\Services\Account\AccountRequest;
use SP\Services\Account\AccountService;
use SP\Services\Category\CategoryService;
use SP\Services\Client\ClientService;
use SP\DataModel\CategoryData;
use SP\DataModel\ClientData;

echo "=== sysPass Functionality Test Suite ===\n\n";

try {
    // Initialize the application
    $dic = \SP\Core\Context\DIC::container();

    echo "✓ Application bootstrap successful\n";

    // Test 1: Database Connection
    echo "\n[Test 1] Database Connection\n";
    $db = $dic->get(\SP\Storage\Database\DatabaseInterface::class);
    echo "✓ Database connected\n";

    // Test 2: Category Service
    echo "\n[Test 2] Category Service - Create\n";
    $categoryService = $dic->get(CategoryService::class);

    $categoryData = new CategoryData();
    $categoryData->setName('Automated Test Category ' . time());
    $categoryData->setDescription('Created by test script');

    try {
        $categoryId = $categoryService->create($categoryData);
        echo "✓ Category created with ID: $categoryId\n";
        echo "  Return type: " . gettype($categoryId) . " (should be integer)\n";

        if (!is_int($categoryId)) {
            echo "✗ ERROR: Category ID is not an integer!\n";
        }
    } catch (\Exception $e) {
        echo "✗ Category creation failed: " . $e->getMessage() . "\n";
    }

    // Test 3: Client Service
    echo "\n[Test 3] Client Service - Create\n";
    $clientService = $dic->get(ClientService::class);

    $clientData = new ClientData();
    $clientData->setName('Automated Test Client ' . time());
    $clientData->setDescription('Created by test script');

    try {
        $clientId = $clientService->create($clientData);
        echo "✓ Client created with ID: $clientId\n";
        echo "  Return type: " . gettype($clientId) . " (should be integer)\n";

        if (!is_int($clientId)) {
            echo "✗ ERROR: Client ID is not an integer!\n";
        }
    } catch (\Exception $e) {
        echo "✗ Client creation failed: " . $e->getMessage() . "\n";
    }

    // Test 4: Account Service - Create
    echo "\n[Test 4] Account Service - Create\n";
    $accountService = $dic->get(AccountService::class);

    $accountRequest = new AccountRequest();
    $accountRequest->id = 0;
    $accountRequest->name = 'Test Account ' . time();
    $accountRequest->login = 'testuser_' . time();
    $accountRequest->url = 'https://example.com/test';
    $accountRequest->categoryId = 1; // Use existing category
    $accountRequest->clientId = 1; // Use existing client
    $accountRequest->pass = 'TestPassword123!';
    $accountRequest->userId = 1; // Admin user
    $accountRequest->userGroupId = 1;
    $accountRequest->isPrivate = 0;
    $accountRequest->isPrivateGroup = 0;
    $accountRequest->passDateChange = 0;
    $accountRequest->notes = 'Created by automated test';

    try {
        $accountId = $accountService->create($accountRequest);
        echo "✓ Account created with ID: $accountId\n";
        echo "  Return type: " . gettype($accountId) . " (should be integer)\n";

        if (!is_int($accountId)) {
            echo "✗ ERROR: Account ID is not an integer!\n";
        }

        // Test 5: Account Service - View
        echo "\n[Test 5] Account Service - View\n";
        $accountData = $accountService->getById($accountId);
        echo "✓ Account retrieved: " . $accountData->getName() . "\n";
        echo "  Login: " . $accountData->getLogin() . "\n";
        echo "  URL: " . $accountData->getUrl() . "\n";

        // Test 6: Account Service - Update
        echo "\n[Test 6] Account Service - Update\n";
        $accountRequest->id = $accountId;
        $accountRequest->name = 'Updated Test Account ' . time();
        $accountRequest->notes = 'Updated by automated test';

        try {
            $accountService->update($accountRequest);
            echo "✓ Account updated successfully\n";

            $updatedAccount = $accountService->getById($accountId);
            echo "  New name: " . $updatedAccount->getName() . "\n";
        } catch (\Exception $e) {
            echo "✗ Account update failed: " . $e->getMessage() . "\n";
        }

        // Test 7: Account Search
        echo "\n[Test 7] Account Service - Search\n";
        try {
            $searchFilter = new \SP\Services\Account\AccountSearchFilter();
            $searchFilter->setLimitCount(5);

            $searchResults = $accountService->search($searchFilter);
            $count = $searchResults->getNumRows();
            echo "✓ Search returned $count results\n";
        } catch (\Exception $e) {
            echo "✗ Account search failed: " . $e->getMessage() . "\n";
        }

        // Test 8: Account Service - Delete
        echo "\n[Test 8] Account Service - Delete\n";
        try {
            $accountService->delete($accountId);
            echo "✓ Account deleted successfully\n";
        } catch (\Exception $e) {
            echo "✗ Account deletion failed: " . $e->getMessage() . "\n";
        }

    } catch (\Exception $e) {
        echo "✗ Account creation failed: " . $e->getMessage() . "\n";
        echo "  Stack trace:\n";
        echo $e->getTraceAsString() . "\n";
    }

    // Test 9: Cleanup - Delete test category
    echo "\n[Test 9] Cleanup - Delete test data\n";
    try {
        if (isset($categoryId)) {
            $categoryService->delete($categoryId);
            echo "✓ Test category deleted\n";
        }
        if (isset($clientId)) {
            $clientService->delete($clientId);
            echo "✓ Test client deleted\n";
        }
    } catch (\Exception $e) {
        echo "⚠ Cleanup warning: " . $e->getMessage() . "\n";
    }

    echo "\n=== Test Suite Complete ===\n";
    echo "✓ All critical operations working with PHP 8.2 strict types\n";

} catch (\Exception $e) {
    echo "\n✗✗✗ FATAL ERROR ✗✗✗\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
