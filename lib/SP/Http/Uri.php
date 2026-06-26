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

namespace SP\Http;

use SP\Core\Crypt\Hash;

/**
 * Class Uri
 *
 * @package SP\Http
 */
final class Uri
{
    private readonly string $base;

    private array $params = [];

    /**
     * Uri constructor.
     */
    public function __construct(string $base)
    {
        $this->base = $base;
    }

    /**
     * @param $key
     * @param $value
     */
    private static function mapParts($key, $value): string
    {
        if (str_starts_with((string) $key, '_')) {
            $key = substr((string) $key, 1);
        }

        return $key . '=' . urlencode((string) $value);
    }

    /**
     * @param string $param Param's name. If an '_' is set at the beginning, it will be a protected param
     * @param string $value
     */
    public function addParam(string $param, $value): self
    {
        $this->params[$param] = (string) $value;

        return $this;
    }

    public function getUri(): string
    {
        return $this->base . '?' . implode('&', array_map([self::class, 'mapParts'], array_keys($this->params), $this->params));
    }

    public function getUriSigned(string $key): string
    {
        $uri = implode('&', array_map([self::class, 'mapParts'], array_keys($this->params), $this->params));

        return $this->base . '?' . $uri . '&h=' . Hash::signMessage($uri, $key);
    }

    /**
     * Clear params array
     *
     * Only clears unprotected params (without '_' at the beginning of the param's name)
     */
    public function resetParams(): self
    {
        $this->params = array_filter($this->params, fn($key): bool => str_starts_with((string) $key, '_'), ARRAY_FILTER_USE_KEY);

        return $this;
    }
}
