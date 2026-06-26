#!/usr/bin/env php
<?php
/**
 * Test PSR-14 Event System Integration
 */

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventDispatcherInterface;
use SP\Core\Events\EventMessage;
use SP\Core\Events\Psr14\Psr14EventAdapter;

define('APP_ROOT', __DIR__);
define('APP_MODULE', 'cli');

require APP_ROOT . '/lib/BaseFunctions.php';
require APP_ROOT . '/vendor/autoload.php';

define('BASE_PATH', __DIR__ . '/lib');
define('APP_PATH', APP_ROOT . '/app');
define('CONFIG_PATH', APP_PATH . '/config');
define('RESOURCES_PATH', APP_PATH . '/resources');
define('CONFIG_FILE', CONFIG_PATH . '/config.xml');
define('ACTIONS_FILE', RESOURCES_PATH . '/actions.xml');
define('MIMETYPES_FILE', RESOURCES_PATH . '/mime.xml');
define('CACHE_PATH', APP_PATH . '/cache');

try {
    echo "=== PSR-14 Event System Test ===\n\n";

    // Build DI container
    echo "[1/5] Building DI container...\n";
    $builder = new ContainerBuilder();
    $builder->writeProxiesToFile(true, CACHE_PATH . '/proxies');
    $builder->addDefinitions(BASE_PATH . '/Definitions.php');
    $builder->useAttributes(true);
    $dic = $builder->build();
    echo "  ✓ Container built successfully\n\n";

    // Test 1: Get EventDispatcher from DI container
    echo "[2/5] Loading EventDispatcher from DI container...\n";
    $eventDispatcher = $dic->get(EventDispatcherInterface::class);
    echo "  ✓ Dispatcher class: " . get_class($eventDispatcher) . "\n";

    if (!($eventDispatcher instanceof PsrEventDispatcherInterface)) {
        echo "  ✗ FAIL: Not a PSR-14 EventDispatcher\n";
        exit(1);
    }
    echo "  ✓ Implements PSR-14 EventDispatcherInterface\n\n";

    // Test 2: Create legacy SplObserver listener
    echo "[3/5] Testing legacy SplObserver compatibility...\n";

    $legacyListener = new class implements SplObserver {
        public bool $called = false;

        public function update(SplSubject $subject): void {
            $this->called = true;
            echo "  → Legacy listener received event\n";
        }
    };

    $eventDispatcher->attach($legacyListener, 'test\\.legacy');

    $testEvent = new Event($legacyListener, new EventMessage('Test legacy event', 'info'));
    $eventDispatcher->notifyEvent('test.legacy.event', $testEvent);

    if ($legacyListener->called) {
        echo "  ✓ Legacy SplObserver listener works\n\n";
    } else {
        echo "  ✗ FAIL: Legacy listener not called\n";
        exit(1);
    }

    // Test 3: Create PSR-14 listener
    echo "[4/5] Testing PSR-14 listener...\n";

    $psr14ListenerCalled = false;
    $psr14Listener = function (Psr14EventAdapter $event) use (&$psr14ListenerCalled) {
        $psr14ListenerCalled = true;
        echo "  → PSR-14 listener received event\n";
        echo "  → Event type: " . $event->getEventType() . "\n";
        echo "  → Event category: " . $event->getCategory() . "\n";
    };

    $eventDispatcher->addListener('test.psr14.event', $psr14Listener);

    $testEvent2 = new Event($psr14Listener, new EventMessage('Test PSR-14 event', 'info'));
    $eventDispatcher->notifyEvent('test.psr14.event', $testEvent2);

    if ($psr14ListenerCalled) {
        echo "  ✓ PSR-14 listener works\n\n";
    } else {
        echo "  ✗ FAIL: PSR-14 listener not called\n";
        exit(1);
    }

    // Test 4: Test both listeners receive the same event
    echo "[5/5] Testing dual dispatch (both legacy and PSR-14)...\n";

    $psr14Count = 0;

    $legacyListener2 = new class implements SplObserver {
        public int $count = 0;

        public function update(SplSubject $subject): void {
            $this->count++;
        }
    };

    $psr14Listener2 = function (Psr14EventAdapter $event) use (&$psr14Count) {
        $psr14Count++;
    };

    $eventDispatcher->attach($legacyListener2, 'test\\.dual');
    $eventDispatcher->addListener('test.dual.event', $psr14Listener2);

    $testEvent3 = new Event($legacyListener2);
    $eventDispatcher->notifyEvent('test.dual.event', $testEvent3);

    if ($legacyListener2->count === 1 && $psr14Count === 1) {
        echo "  ✓ Both legacy and PSR-14 listeners received the event\n\n";
    } else {
        echo "  ✗ FAIL: Legacy count={$legacyListener2->count}, PSR-14 count=$psr14Count\n";
        exit(1);
    }

    echo "=== All Tests Passed ===\n";
    echo "\nSummary:\n";
    echo "  ✓ PSR-14 adapter loaded from DI container\n";
    echo "  ✓ Legacy SplObserver listeners work\n";
    echo "  ✓ PSR-14 listeners work\n";
    echo "  ✓ Dual dispatch to both systems\n";
    echo "\nThe PSR-14 event system is fully functional with backward compatibility!\n";

} catch (Throwable $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
