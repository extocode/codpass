<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

/*
 * Rector Configuration for sysPass
 *
 * Rector automates PHP code refactoring and upgrades.
 *
 * Usage:
 *   composer rector      - Dry run (show the changes, but do not apply them)
 *   composer rector:fix  - Apply the changes
 *
 * Strategy:
 *   1. Start with the current PHP version (7.4)
 *   2. Run rector to clean up the code
 *   3. Upgrade the PHP version target step by step
 *   4. Enable more rule sets as needed
 */

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/lib/SP',
        __DIR__ . '/app/modules/web',
        __DIR__ . '/app/modules/api',
    ])
    ->withSkip([
        __DIR__ . '/vendor',
        __DIR__ . '/app/cache',
        __DIR__ . '/app/temp',
        __DIR__ . '/app/backup',
        __DIR__ . '/tests',  // Enable tests later

        // Skip the rules that cause conflicts
        ClassPropertyAssignToConstructorPromotionRector::class,
        TypedPropertyFromStrictConstructorRector::class,
    ])

    // PHP 8.2 target (automatically includes all earlier versions)
    ->withPhpSets(php82: true)

    ->withSets([
        // Code quality improvements (safe)
        SetList::CODE_QUALITY,

        // Dead code removal
        SetList::DEAD_CODE,

        // Type declarations (adds return types, parameter types)
        SetList::TYPE_DECLARATION,

        // Coding style consistency
        // SetList::CODING_STYLE,

        // Early return pattern
        // SetList::EARLY_RETURN,
    ])

    // Individual rules (uncomment as needed)
    ->withRules([
        // Add individual rules here if needed
    ])

    // Parallel processing
    ->withParallel();
