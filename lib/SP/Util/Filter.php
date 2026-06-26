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

namespace SP\Util;

defined('APP_ROOT') || die();

/**
 * Class Filter para el filtrado de datos
 *
 * @package SP\Util
 */
final class Filter
{
    /**
     * Limpiar una cadena de búsqueda de carácteres utilizados en expresiones regulares
     *
     * @param $string
     */
    public static function safeSearchString($string): string
    {
        return str_replace(['/', '[', '\\', ']', '%', '{', '}', '*', '$'], '', (string) $string);
    }

    /**
     * @param $value
     */
    public static function getEmail($value): string
    {
        return filter_var(trim((string) $value), FILTER_SANITIZE_EMAIL);
    }

    public static function getArray(array $array): array
    {
        return array_map(function ($value): int|string|null {
            if ($value !== null) {
                if (is_numeric($value)) {
                    return Filter::getInt($value);
                }
                return Filter::getString($value);
            }

            return null;
        }, $array);
    }

    /**
     * @param $value
     */
    public static function getInt($value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @param $value
     */
    public static function getString($value): string
    {
        return strip_tags(trim((string) $value));
    }

    /**
     * @param $value
     */
    public static function getRaw($value): string
    {
        return filter_var(trim((string) $value), FILTER_UNSAFE_RAW);
    }
}
