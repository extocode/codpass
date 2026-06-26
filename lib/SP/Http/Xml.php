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

use Klein\Response;

/**
 * Class Xml
 *
 * @package SP\Http
 */
final readonly class Xml
{
    public const SAFE = [
        'from' => ['&', '<', '>', '"', "\'"],
        'to' => ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'],
    ];

    private \Klein\Response $response;

    /**
     * Xml constructor.
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Devuelve una respuesta en formato XML con el estado y el mensaje.
     *
     * @param string $description mensaje a devolver
     * @param int    $status      devuelve el estado
     */
    public function printXml(string $description, int $status = 1): void
    {
        if (!is_string($description)) {
            return;
        }

        $xml[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml[] = '<root>';
        $xml[] = '<status>' . $status . '</status>';
        $xml[] = '<description>' . $this->safeString($description) . '</description>';
        $xml[] = '</root>';

        $this->response
            ->header('Content-Type', 'application/xml')
            ->body(implode(PHP_EOL, $xml))
            ->send(true);
    }

    public function safeString(string $string): string
    {
        return str_replace(self::SAFE['from'], self::SAFE['to'], $string);
    }
}
