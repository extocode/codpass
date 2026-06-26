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

/**
 * Class Checks utilidades de comprobación
 *
 * @package SP\Util
 */
final class Checks
{
    /**
     * Comprobar si sysPass se ejecuta en W$indows.
     */
    public static function checkIsWindows(): bool
    {
        return str_starts_with(PHP_OS, 'WIN');
    }

    /**
     * Comprobar la versión de PHP.
     */
    public static function checkPhpVersion(): bool
    {
        return version_compare(PHP_VERSION, '8.2', '>=')
            && version_compare(PHP_VERSION, '9.0', '<');
    }
}
