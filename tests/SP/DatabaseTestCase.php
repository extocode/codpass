<?php
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SP\Core\Exceptions\SPException;
use SP\Storage\Database\DatabaseConnectionData;

/**
 * Class DatabaseTestCase
 *
 * Base test case for tests that require database queries.
 * Replaces abandoned PHPUnit\DbUnit with custom XML dataset loading.
 *
 * @package SP\Tests
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * @var DatabaseConnectionData
     */
    protected static $databaseConnectionData;

    /**
     * @var string
     */
    protected static $dataset = 'syspass.xml';

    /**
     * @var PDO
     */
    private static $pdo;

    /**
     * @var bool
     */
    private static $datasetLoaded = false;

    /**
     * Returns the test database connection.
     *
     * @return PDO
     * @throws SPException
     */
    final public function getConnection()
    {
        if (self::$pdo === null) {
            self::$pdo = getDbHandler()->getConnection();
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }

    /**
     * Loads the dataset before each test.
     *
     * @throws SPException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Load dataset only once per test class
        if (!self::$datasetLoaded) {
            $this->loadDataSet();
            self::$datasetLoaded = true;
        }
    }

    /**
     * Resets the dataset loaded flag after each test class.
     */
    public static function tearDownAfterClass(): void
    {
        self::$datasetLoaded = false;
        self::$pdo = null;
    }

    /**
     * Loads and executes the XML dataset.
     *
     * @throws SPException
     */
    protected function loadDataSet()
    {
        $datasetPath = RESOURCE_DIR . DIRECTORY_SEPARATOR . 'datasets' . DIRECTORY_SEPARATOR . self::$dataset;

        if (!file_exists($datasetPath)) {
            throw new SPException("Dataset file not found: {$datasetPath}");
        }

        $pdo = $this->getConnection();

        // Disable foreign key checks temporarily
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        // Load XML dataset
        $xml = simplexml_load_file($datasetPath);

        if ($xml === false) {
            throw new SPException("Failed to parse dataset XML: {$datasetPath}");
        }

        // Clear all tables first
        $this->clearTables($pdo, $xml);

        // Insert data from XML
        $this->insertDataFromXml($pdo, $xml);

        // Re-enable foreign key checks
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Clears all tables defined in the dataset.
     *
     * @param PDO              $pdo
     * @param SimpleXMLElement $xml
     */
    private function clearTables(PDO $pdo, SimpleXMLElement $xml)
    {
        $tables = [];

        // Collect all table names
        foreach ($xml->database->table_data as $tableData) {
            $tableName = (string)$tableData['name'];
            if (!in_array($tableName, $tables)) {
                $tables[] = $tableName;
            }
        }

        // Truncate all tables
        foreach ($tables as $table) {
            try {
                $pdo->exec("TRUNCATE TABLE `{$table}`");
            } catch (\PDOException $e) {
                // If truncate fails, try delete
                $pdo->exec("DELETE FROM `{$table}`");
            }
        }
    }

    /**
     * Inserts data from XML into database.
     *
     * @param PDO              $pdo
     * @param SimpleXMLElement $xml
     * @throws SPException
     */
    private function insertDataFromXml(PDO $pdo, SimpleXMLElement $xml)
    {
        foreach ($xml->database->table_data as $tableData) {
            $tableName = (string)$tableData['name'];

            foreach ($tableData->row as $row) {
                $fields = [];
                $values = [];
                $placeholders = [];

                foreach ($row->field as $field) {
                    $fieldName = (string)$field['name'];
                    $fields[] = "`{$fieldName}`";
                    $placeholders[] = '?';

                    // Check for xsi namespace attributes
                    $attributes = $field->attributes('xsi', true);

                    // Handle NULL values (xsi:nil="true")
                    if (isset($attributes['nil']) && (string)$attributes['nil'] === 'true') {
                        $values[] = null;
                    } elseif (isset($attributes['type']) && (string)$attributes['type'] === 'xs:hexBinary') {
                        // Handle hexBinary type - convert hex string to binary
                        $values[] = hex2bin((string)$field);
                    } else {
                        $values[] = (string)$field;
                    }
                }

                if (empty($fields)) {
                    continue;
                }

                $sql = sprintf(
                    'INSERT INTO `%s` (%s) VALUES (%s)',
                    $tableName,
                    implode(', ', $fields),
                    implode(', ', $placeholders)
                );

                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
            }
        }
    }

    /**
     * Returns the test dataset path.
     * Kept for backward compatibility with tests that may reference it.
     *
     * @return string
     */
    protected function getDataSet()
    {
        return RESOURCE_DIR . DIRECTORY_SEPARATOR . 'datasets' . DIRECTORY_SEPARATOR . self::$dataset;
    }

    /**
     * Asserts that a table has a specific number of rows.
     *
     * @param string $table
     * @param int    $expected
     * @param string $message
     * @throws SPException
     */
    protected function assertTableRowCount(string $table, int $expected, string $message = '')
    {
        $pdo = $this->getConnection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
        $actual = (int)$stmt->fetchColumn();

        $this->assertEquals($expected, $actual, $message ?: "Table {$table} should have {$expected} rows");
    }

    /**
     * Asserts that a row exists in a table with specific conditions.
     *
     * @param string $table
     * @param array  $conditions Key-value pairs for WHERE clause
     * @param string $message
     * @throws SPException
     */
    protected function assertTableHasRow(string $table, array $conditions, string $message = '')
    {
        $pdo = $this->getConnection();

        $where = [];
        $values = [];

        foreach ($conditions as $column => $value) {
            $where[] = "`{$column}` = ?";
            $values[] = $value;
        }

        $sql = sprintf('SELECT COUNT(*) FROM `%s` WHERE %s', $table, implode(' AND ', $where));
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        $count = (int)$stmt->fetchColumn();

        $this->assertGreaterThan(0, $count, $message ?: "Table {$table} should have a row matching conditions");
    }
}