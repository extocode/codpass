<?php

declare(strict_types=1);

/**
 * Doctrine Migrations Configuration
 *
 * This file configures Doctrine Migrations for sysPass database schema management.
 * Database connection is loaded from the sysPass configuration.
 *
 * Usage:
 *   php bin/console migrations:status
 *   php bin/console migrations:migrate
 *   php bin/console migrations:diff
 *
 * @see https://www.doctrine-project.org/projects/doctrine-migrations/en/3.7/reference/configuration.html
 */

return [
    'table_storage' => [
        'table_name' => 'doctrine_migration_versions',
        'version_column_name' => 'version',
        'version_column_length' => 191,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],

    'migrations_paths' => [
        'SP\\Migrations' => 'migrations',
    ],

    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null, // Will be injected at runtime
    'em' => null,
];
