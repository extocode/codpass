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

use Psr\EventDispatcher\StoppableEventInterface;
use SP\Core\Events\Event;

/**
 * Class Psr14EventAdapter
 *
 * PSR-14 compliant event wrapper that adapts the legacy sysPass Event class
 * to PSR-14 standards while maintaining backward compatibility.
 *
 * This adapter:
 * - Implements PSR-14 StoppableEventInterface
 * - Wraps the legacy Event and EventMessage objects
 * - Provides access to event data in both old and new formats
 * - Supports event propagation stopping
 *
 * @package SP\Core\Events\Psr14
 */
final class Psr14EventAdapter implements StoppableEventInterface
{
    /**
     * Whether event propagation has been stopped
     */
    private bool $propagationStopped = false;

    /**
     * The event type name (e.g., 'create.account')
     */
    private string $eventType;

    /**
     * The legacy Event object
     */
    private Event $legacyEvent;

    /**
     * Constructor
     *
     * @param string $eventType Event type string
     * @param Event $legacyEvent Legacy sysPass Event object
     */
    public function __construct(string $eventType, Event $legacyEvent)
    {
        $this->eventType = $eventType;
        $this->legacyEvent = $legacyEvent;
    }

    /**
     * PSR-14: Check if event propagation has been stopped
     *
     * @return bool True if propagation should stop, false otherwise
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stop event propagation
     *
     * Prevents subsequent listeners from being called
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Get the event type string
     *
     * @return string Event type (e.g., 'create.account', 'login.auth.browser')
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * Get the legacy Event object for backward compatibility
     *
     * @return Event
     */
    public function getLegacyEvent(): Event
    {
        return $this->legacyEvent;
    }

    /**
     * Get the event source object
     *
     * @return object The object that triggered the event
     */
    public function getSource(): object
    {
        return $this->legacyEvent->getSource();
    }

    /**
     * Get typed source object
     *
     * @param string $type Expected type name
     * @return object The source object cast to the expected type
     * @throws \RuntimeException If type doesn't match
     */
    public function getTypedSource(string $type): object
    {
        return $this->legacyEvent->getSource($type);
    }

    /**
     * Get the EventMessage if present
     *
     * @return \SP\Core\Events\EventMessage|null
     */
    public function getMessage(): ?\SP\Core\Events\EventMessage
    {
        return $this->legacyEvent->getEventMessage();
    }

    /**
     * Check if this is a specific event type
     *
     * @param string $eventType Event type to check
     * @return bool True if matches
     */
    public function is(string $eventType): bool
    {
        return $this->eventType === $eventType;
    }

    /**
     * Check if event type matches a pattern (regex)
     *
     * @param string $pattern Regex pattern (without delimiters)
     * @return bool True if matches
     */
    public function matches(string $pattern): bool
    {
        return (bool) preg_match('/' . $pattern . '/i', $this->eventType);
    }

    /**
     * Check if this is a CRUD operation event
     *
     * @return bool
     */
    public function isCrudOperation(): bool
    {
        return $this->matches('^(create|edit|delete|show)\.');
    }

    /**
     * Check if this is a creation event
     *
     * @return bool
     */
    public function isCreate(): bool
    {
        return $this->matches('^create\.');
    }

    /**
     * Check if this is an edit event
     *
     * @return bool
     */
    public function isEdit(): bool
    {
        return $this->matches('^edit\.');
    }

    /**
     * Check if this is a delete event
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->matches('^delete\.');
    }

    /**
     * Check if this is an authentication event
     *
     * @return bool
     */
    public function isAuth(): bool
    {
        return $this->matches('(login|logout)');
    }

    /**
     * Get event category (first part before dot)
     *
     * @return string Category (e.g., 'create', 'login', 'database')
     */
    public function getCategory(): string
    {
        $parts = explode('.', $this->eventType);
        return $parts[0] ?? '';
    }

    /**
     * Get event sub-type (part after first dot)
     *
     * @return string Sub-type (e.g., 'account', 'user')
     */
    public function getSubType(): string
    {
        $parts = explode('.', $this->eventType, 2);
        return $parts[1] ?? '';
    }

    /**
     * String representation for debugging
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            'Psr14EventAdapter[type=%s, source=%s, stopped=%s]',
            $this->eventType,
            get_class($this->getSource()),
            $this->propagationStopped ? 'yes' : 'no'
        );
    }
}
