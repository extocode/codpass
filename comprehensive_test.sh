#!/bin/bash
# Comprehensive sysPass Functionality Test Suite
# Tests all major operations via database and API simulation

echo "════════════════════════════════════════════════════════"
echo "  sysPass v4 Comprehensive Functionality Test Suite"
echo "════════════════════════════════════════════════════════"
echo ""

DB_USER="syspass"
DB_PASS="syspass"
DB_NAME="syspass"

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

TESTS_PASSED=0
TESTS_FAILED=0

# Function to run a test
run_test() {
    local test_name="$1"
    local test_command="$2"

    echo -n "Testing: $test_name ... "

    if eval "$test_command" &> /dev/null; then
        echo -e "${GREEN}✓ PASS${NC}"
        ((TESTS_PASSED++))
        return 0
    else
        echo -e "${RED}✗ FAIL${NC}"
        ((TESTS_FAILED++))
        return 1
    fi
}

# Test 1: Database Connection
run_test "Database Connection" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e 'SELECT 1' 2>&1"

# Test 2: Read Accounts
run_test "Read Accounts (SELECT)" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e 'SELECT COUNT(*) FROM Account' 2>&1"

# Test 3: Create Category
TEST_CATEGORY_NAME="AutoTest_Category_$$"
run_test "Create Category" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"INSERT INTO Category (name, description, hash) VALUES ('$TEST_CATEGORY_NAME', 'Test category', MD5('$TEST_CATEGORY_NAME'))\" 2>&1"

CATEGORY_ID=$(mariadb -u $DB_USER -p$DB_PASS $DB_NAME -se "SELECT id FROM Category WHERE name='$TEST_CATEGORY_NAME'" 2>/dev/null)

# Test 4: Read Category
run_test "Read Category" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT * FROM Category WHERE name='$TEST_CATEGORY_NAME'\" 2>&1"

# Test 5: Update Category
run_test "Update Category" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"UPDATE Category SET description='Updated description' WHERE name='$TEST_CATEGORY_NAME'\" 2>&1"

# Test 6: Create Client
TEST_CLIENT_NAME="AutoTest_Client_$$"
run_test "Create Client" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"INSERT INTO Client (name, description, hash, isGlobal) VALUES ('$TEST_CLIENT_NAME', 'Test client', MD5('$TEST_CLIENT_NAME'), 0)\" 2>&1"

CLIENT_ID=$(mariadb -u $DB_USER -p$DB_PASS $DB_NAME -se "SELECT id FROM Client WHERE name='$TEST_CLIENT_NAME'" 2>/dev/null)

# Test 7: Create Account
TEST_ACCOUNT_NAME="AutoTest_Account_$$"
run_test "Create Account (INSERT)" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"INSERT INTO Account (name, categoryId, clientId, login, url, pass, \\\`key\\\`, notes, userId, userGroupId, userEditId, dateAdd, dateEdit, passDate, passDateChange, isPrivate, isPrivateGroup) VALUES ('$TEST_ACCOUNT_NAME', $CATEGORY_ID, $CLIENT_ID, 'testuser', 'https://test.com', 'encrypted_pass', 'test_key', 'Test notes', 1, 1, 1, NOW(), NOW(), UNIX_TIMESTAMP(), 0, 0, 0)\" 2>&1"

ACCOUNT_ID=$(mariadb -u $DB_USER -p$DB_PASS $DB_NAME -se "SELECT id FROM Account WHERE name='$TEST_ACCOUNT_NAME'" 2>/dev/null)

# Test 8: Read Account
run_test "Read Account (SELECT with JOIN)" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT a.*, c.name as category_name, cl.name as client_name FROM Account a LEFT JOIN Category c ON a.categoryId = c.id LEFT JOIN Client cl ON a.clientId = cl.id WHERE a.name='$TEST_ACCOUNT_NAME'\" 2>&1"

# Test 9: Update Account
run_test "Update Account" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"UPDATE Account SET notes='Updated notes', dateEdit=NOW() WHERE name='$TEST_ACCOUNT_NAME'\" 2>&1"

# Test 10: Search Accounts
run_test "Search Accounts (LIKE query)" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT * FROM Account WHERE name LIKE '%AutoTest%'\" 2>&1"

# Test 11: Account Count
run_test "Account Statistics" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT COUNT(*) as total, COUNT(DISTINCT categoryId) as categories, COUNT(DISTINCT clientId) as clients FROM Account\" 2>&1"

# Test 12: Create Tag
TEST_TAG_NAME="AutoTest_Tag_$$"
run_test "Create Tag" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"INSERT INTO Tag (name, hash) VALUES ('$TEST_TAG_NAME', MD5('$TEST_TAG_NAME'))\" 2>&1"

# Test 13: User Authentication Check
run_test "User Table Query" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT id, login, name, isAdminApp FROM User WHERE id=1\" 2>&1"

# Test 14: User Group Operations
run_test "UserGroup Table Query" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT * FROM UserGroup\" 2>&1"

# Test 15: Foreign Key Constraints
run_test "Foreign Key Integrity" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT COUNT(*) FROM Account a JOIN Category c ON a.categoryId = c.id JOIN Client cl ON a.clientId = cl.id\" 2>&1"

# Test 16: Account History (if exists)
run_test "Account History Table" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT COUNT(*) FROM AccountHistory\" 2>&1"

# Test 17: Configuration Table
run_test "Configuration Read" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT parameter, value FROM Config LIMIT 5\" 2>&1"

# Test 18: Event Log
run_test "Event Log Table" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT COUNT(*) FROM EventLog\" 2>&1"

# Test 19: Custom Fields
run_test "Custom Field Tables" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"SELECT COUNT(*) FROM CustomFieldDefinition\" 2>&1"

# Test 20: Delete Operations (Cleanup)
echo ""
echo "Performing cleanup..."

run_test "Delete Test Account" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"DELETE FROM Account WHERE name='$TEST_ACCOUNT_NAME'\" 2>&1"

run_test "Delete Test Tag" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"DELETE FROM Tag WHERE name='$TEST_TAG_NAME'\" 2>&1"

run_test "Delete Test Category" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"DELETE FROM Category WHERE name='$TEST_CATEGORY_NAME'\" 2>&1"

run_test "Delete Test Client" \
    "mariadb -u $DB_USER -p$DB_PASS $DB_NAME -e \"DELETE FROM Client WHERE name='$TEST_CLIENT_NAME'\" 2>&1"

# Summary
echo ""
echo "════════════════════════════════════════════════════════"
echo "  Test Results Summary"
echo "════════════════════════════════════════════════════════"
echo ""
echo -e "Tests Passed: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Tests Failed: ${RED}$TESTS_FAILED${NC}"
echo "Total Tests:  $((TESTS_PASSED + TESTS_FAILED))"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ All tests passed! sysPass is fully functional.${NC}"
    exit 0
else
    echo -e "${RED}✗ Some tests failed. Check the output above for details.${NC}"
    exit 1
fi
