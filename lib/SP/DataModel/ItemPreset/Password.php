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

namespace SP\DataModel\ItemPreset;

/**
 * Class Password
 *
 * @package SP\DataModel\ItemPreset
 */
class Password
{
    public const EXPIRE_TIME_MULTIPLIER = 86400;

    private int $length = 0;

    private bool $useNumbers = false;

    private bool $useLetters = false;

    private bool $useSymbols = false;

    private bool $useUpper = false;

    private bool $useLower = false;

    private bool $useImage = false;

    private int $expireTime = 0;

    private int $score = 0;

    private ?string $regex = null;

    public function getRegex(): string
    {
        return $this->regex ?: '';
    }

    public function setRegex(string $regex): self
    {
        $this->regex = $regex;
        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function isUseNumbers(): bool
    {
        return $this->useNumbers;
    }

    public function setUseNumbers(bool $useNumbers): self
    {
        $this->useNumbers = $useNumbers;
        return $this;
    }

    public function isUseLetters(): bool
    {
        return $this->useLetters;
    }

    public function setUseLetters(bool $useLetters): self
    {
        $this->useLetters = $useLetters;
        return $this;
    }

    public function isUseSymbols(): bool
    {
        return $this->useSymbols;
    }

    public function setUseSymbols(bool $useSymbols): self
    {
        $this->useSymbols = $useSymbols;
        return $this;
    }

    public function isUseUpper(): bool
    {
        return $this->useUpper;
    }

    public function setUseUpper(bool $useUpper): self
    {
        $this->useUpper = $useUpper;
        return $this;
    }

    public function isUseLower(): bool
    {
        return $this->useLower;
    }

    public function setUseLower(bool $useLower): self
    {
        $this->useLower = $useLower;
        return $this;
    }

    public function isUseImage(): bool
    {
        return $this->useImage;
    }

    public function setUseImage(bool $useImage): self
    {
        $this->useImage = $useImage;
        return $this;
    }

    public function getExpireTime(): int
    {
        return $this->expireTime;
    }

    public function setExpireTime(int $expireTime): self
    {
        $this->expireTime = $expireTime * self::EXPIRE_TIME_MULTIPLIER;

        return $this;
    }
}
