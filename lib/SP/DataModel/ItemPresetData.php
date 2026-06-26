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

namespace SP\DataModel;

/**
 * Class ItemPresetData
 *
 * @package SP\DataModel
 */
class ItemPresetData extends DataModelBase implements HydratableInterface
{
    use SerializedModel;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $userGroupId;

    /**
     * @var int
     */
    public $userProfileId;

    /**
     * @var int
     */
    public $fixed;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var string
     */
    public $data;

    public function getId(): int
    {
        return $this->id !== null ? (int) $this->id : null;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId !== null ? (int) $this->userId : null;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserGroupId(): ?int
    {
        return $this->userGroupId !== null ? (int) $this->userGroupId : null;
    }

    public function setUserGroupId(int $userGroupId): static
    {
        $this->userGroupId = $userGroupId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserProfileId(): ?int
    {
        return $this->userProfileId !== null ? (int) $this->userProfileId : null;
    }

    public function setUserProfileId(int $userProfileId): static
    {
        $this->userProfileId = $userProfileId;

        return $this;
    }

    public function getFixed(): int
    {
        return (int) $this->fixed;
    }

    public function setFixed(int $fixed): static
    {
        $this->fixed = $fixed;

        return $this;
    }

    public function getPriority(): int
    {
        return (int) $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getHash(): string
    {
        return sha1($this->type . (int) $this->userId . (int) $this->userGroupId . (int) $this->userProfileId . (int) $this->priority);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
