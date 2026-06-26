<?php

declare(strict_types=1);

/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2019, Rubén Domínguez nuxsmin@$syspass.org
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

namespace SP\Core\Events\Psr14;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Psr14EventDispatcherAdapter
 *
 * PSR-14 compliant event dispatcher adapter that maintains backward compatibility
 * with the existing sysPass event system.
 *
 * This adapter:
 * - Implements both sysPass EventDispatcherInterface and PSR-14 EventDispatcherInterface
 * - Wraps Symfony EventDispatcher for PSR-14 compliance
 * - Maintains SplSubject/SplObserver compatibility for existing listeners
 * - Allows gradual migration to PSR-14
 *
 * @package SP\Core\Events\Psr14
 */
final class Psr14EventDispatcherAdapter implements EventDispatcherInterface, PsrEventDispatcherInterface
{
    /**
     * Array of legacy SplObserver listeners
     *
     * @var \SplObserver[]
     */
    private array $observers = [];

    /**
     * Symfony PSR-14 EventDispatcher
     */
    private EventDispatcher $symfonyDispatcher;

    public function __construct()
    {
        $this->symfonyDispatcher = new EventDispatcher();
    }

    /**
     * Attach a legacy SplObserver listener (backward compatibility)
     *
     * @param \SplObserver $observer
     */
    public function attach(\SplObserver $observer): void
    {
        $hash = spl_object_hash($observer);

        if (!isset($this->observers[$hash])) {
            $this->observers[$hash] = $observer;
        }
    }

    /**
     * Detach a legacy SplObserver listener (backward compatibility)
     *
     * @param \SplObserver $observer
     */
    public function detach(\SplObserver $observer): void
    {
        $hash = spl_object_hash($observer);

        if (isset($this->observers[$hash])) {
            unset($this->observers[$hash]);
        }
    }

    /**
     * Notify legacy observers (backward compatibility)
     * This maintains the original sysPass event system behavior
     */
    public function notify(): void
    {
        // No-op for backward compatibility
        // Actual notification happens in notifyEvent()
    }

    /**
     * Notify an event using the original sysPass event system
     * Maintains backward compatibility while also dispatching PSR-14 events
     *
     * @param string $eventType Event type string (e.g., 'create.account')
     * @param Event $event Event object containing source and message
     */
    public function notifyEvent($eventType, Event $event): void
    {
        // Dispatch to legacy observers (existing behavior)
        $this->notifyLegacyObservers($eventType, $event);

        // Dispatch PSR-14 event (new behavior)
        $psr14Event = new Psr14EventAdapter($eventType, $event);
        $this->dispatch($psr14Event, $eventType);
    }

    /**
     * Notify legacy SplObserver listeners using the original pattern matching logic
     *
     * @param string $eventType
     * @param Event $event
     */
    private function notifyLegacyObservers(string $eventType, Event $event): void
    {
        foreach ($this->observers as $observer) {
            // Check if observer has event filtering
            if (method_exists($observer, 'getEventsString')) {
                $events = $observer->getEventsString();

                // Match all events or use regex pattern matching
                if (!empty($events) && ($events === '*' || preg_match('/' . $events . '/i', $eventType))) {
                    if (method_exists($observer, 'updateEvent')) {
                        $observer->updateEvent($eventType, $event);
                    } else {
                        $observer->update($this);
                    }
                }
            } else {
                // No filtering, send all events
                if (method_exists($observer, 'updateEvent')) {
                    $observer->updateEvent($eventType, $event);
                } else {
                    $observer->update($this);
                }
            }
        }
    }

    /**
     * PSR-14 dispatch method
     *
     * Dispatches an event to all registered PSR-14 listeners
     *
     * @param object $event The event object
     * @param string|null $eventName Optional event name
     * @return object The same event object
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        return $this->symfonyDispatcher->dispatch($event, $eventName);
    }

    /**
     * Add a PSR-14 event listener
     *
     * @param string $eventName The event name
     * @param callable $listener The listener callable
     * @param int $priority Priority (higher = earlier execution)
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->symfonyDispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Add a PSR-14 event subscriber
     *
     * @param \Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber
     */
    public function addSubscriber(\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber): void
    {
        $this->symfonyDispatcher->addSubscriber($subscriber);
    }

    /**
     * Remove a PSR-14 event listener
     *
     * @param string $eventName
     * @param callable $listener
     */
    public function removeListener(string $eventName, callable $listener): void
    {
        $this->symfonyDispatcher->removeListener($eventName, $listener);
    }

    /**
     * Get the Symfony EventDispatcher instance for advanced usage
     *
     * @return EventDispatcher
     */
    public function getSymfonyDispatcher(): EventDispatcher
    {
        return $this->symfonyDispatcher;
    }

    /**
     * Get count of legacy observers
     *
     * @return int
     */
    public function getLegacyObserverCount(): int
    {
        return count($this->observers);
    }

    /**
     * Check if a PSR-14 listener exists
     *
     * @param string $eventName
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return $this->symfonyDispatcher->hasListeners($eventName);
    }
}
