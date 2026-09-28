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
 * PSR-14 compliant event wrapper. It adapts the legacy sysPass Event class
 * to the PSR-14 standards and keeps backward compatibility.
 *
 * This adapter:
 * - Implements PSR-14 StoppableEventInterface
 * - Wraps the legacy Event and EventMessage objects
 * - Gives access to the event data in the old and the new formats
 * - Supports a stop of the event propagation
 *
 * @package SP\Core\Events\Psr14
 */
final class Psr14EventAdapter implements StoppableEventInterface
{
    /**
     * Shows if the event propagation is stopped
     */
    private bool $propagationStopped = false;

    /**
     * The event type name (for example, 'create.account')
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
     * PSR-14: Check if the event propagation is stopped
     *
     * @return bool True if the propagation must stop, false otherwise
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stop the event propagation
     *
     * Prevents calls to the subsequent listeners
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Get the event type string
     *
     * @return string Event type (for example, 'create.account' or 'login.auth.browser')
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
     * @throws \RuntimeException If the type does not match
     */
    public function getTypedSource(string $type): object
    {
        return $this->legacyEvent->getSource($type);
    }

    /**
     * Get the EventMessage if it exists
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
     * Check if the event type matches a pattern (regex)
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
     * Get the event category (the first part before the dot)
     *
     * @return string Category (for example, 'create', 'login' or 'database')
     */
    public function getCategory(): string
    {
        $parts = explode('.', $this->eventType);
        return $parts[0] ?? '';
    }

    /**
     * Get the event sub-type (the part after the first dot)
     *
     * @return string Sub-type (for example, 'account' or 'user')
     */
    public function getSubType(): string
    {
        $parts = explode('.', $this->eventType, 2);
        return $parts[1] ?? '';
    }

    /**
     * String representation for debug output
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
