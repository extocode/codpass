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

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use DOMNodeList;
use InvalidArgumentException;

/**
 * Class XMLRPCResponseParse para el parseo de respuestas HTTP en formato XML-RPC
 *
 * @package SP\Http
 */
abstract class XMLRPCResponseParse
{
    private ?\DOMElement $root = null;

    /**
     * @var string
     */
    private $xml;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param string $xml El documento XML
     *
     * @throws InvalidArgumentException
     */
    public function __construct($xml)
    {
        try {
            $this->xml = $xml;

            $dom = new DOMDocument();
            $dom->loadXML($xml);

            if ($dom->getElementsByTagName('methodResponse')->length === 0) {
                throw new DOMException(__u('Invalid XML-RPC response'));
            }

            $this->root = $dom->documentElement;
        } catch (DOMException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Obtener los datos del error
     *
     * @return array
     */
    public function getError()
    {
        return $this->parseNodes($this->root->getElementsByTagName('fault'));
    }

    /**
     * Obtener los nodos recursivamente y almacenar los datos en el atributo
     * de la clase _data
     *
     *
     * @return array
     */
    private function parseNodes(DOMNodeList $nodes)
    {
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                if ($node instanceof DOMElement) {
                    /**
                     * @var $node DOMElement
                     */
                    switch ($node->nodeName) {
                        case 'struct':
                            return $this->parseStruct($node);
                        case 'array':
                            return $this->parseArray($node);
                        case 'fault':
                            return $this->parseFault($node);
                        case 'value':
                            $this->data = $this->parseValues($node);
                            break;
                        default:
                            $this->parseNodes($node->childNodes);
                    }
                }
            }
        }

        return [];
    }

    /**
     * Procesar nodos del tipo struct
     *
     */
    private function parseStruct(DOMElement $xmlStruct): array
    {
        $dataStruct = [];
        $nStruct = 0;

        foreach ($xmlStruct->childNodes as $struct) {
            if ($struct instanceof DOMElement) {
                foreach ($struct->childNodes as $member) {
                    /**
                     * @var $member DOMNode
                     */
                    switch ($member->nodeName) {
                        case 'name':
                            $name = $member->nodeValue;
                            break;
                        case 'value':
                            $dataStruct[$name] = $this->parseNodeType($member->firstChild);
                            break;
                    }
                }
                $nStruct++;
            }
        }

        return $dataStruct;
    }

    private function parseNodeType(DOMNode $node): int|string|bool|null
    {
        return match ($node->nodeName) {
            'int', 'i4' => (int) $node->nodeValue,
            'string' => $node->nodeValue,
            'dateTime.iso8601' => date('d M y H:i:s', strtotime((string) $node->nodeValue)),
            'boolean' => (bool) $node->nodeValue,
            default => null,
        };
    }

    /**
     * Procesar nodos del tipo array
     *
     */
    private function parseArray(DOMElement $xmlArray): array
    {
        $arrayData = [];

        foreach ($xmlArray->childNodes as $array) {
            foreach ($array->childNodes as $data) {
                /**
                 * @var $data DOMElement
                 */
                if ($data instanceof DOMElement && $data->nodeName === 'value') {
                    $values = $this->parseValues($data);

                    if (is_array($values)) {
                        $arrayData[] = $values;
                    }
                }
            }
        }

        return $arrayData;
    }

    /**
     * Procesar nodos del tipo value
     *
     *
     * @return array
     */
    private function parseValues(DOMElement $xmlValues)
    {
        $valuesData = [];

        foreach ($xmlValues->childNodes as $xmlValue) {
            if ($xmlValue instanceof DOMElement) {
                $val = $this->parseNodeType($xmlValue);

                if (null === $val) {
                    return $this->parseNodes($xmlValues->childNodes);
                }
                $valuesData[] = $val;
            }
        }

        return $valuesData;
    }

    /**
     * Procesar nodos del tipo fault
     *
     */
    private function parseFault(DOMElement $xmlFault): array
    {
        $faultData = [];

        foreach ($xmlFault->childNodes as $fault) {
            /**
             * @var $fault DOMElement
             */
            if ($fault instanceof DOMElement && $fault->nodeName === 'value') {
                $values = $this->parseValues($fault);

                if (is_array($values)) {
                    return $values;
                }
            }
        }

        return $faultData;
    }

    /**
     * Obtener los datos de la respuesta
     */
    public function parseParams()
    {
        $this->parseNodes($this->root->getElementsByTagName('params'));

        return $this->data;
    }

    /**
     * Devolver el documento XML
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }
}
