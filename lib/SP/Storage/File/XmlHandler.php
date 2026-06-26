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

namespace SP\Storage\File;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use ReflectionObject;
use RuntimeException;

/**
 * Class XmlHandler para manejo básico de documentos XML
 *
 * @package SP\Storage\File;
 */
final class XmlHandler implements XmlFileStorageInterface
{
    /**
     * @var mixed
     */
    protected $items;

    private ?\DOMDocument $Dom = null;

    private \DOMElement|bool|null $root = null;

    private readonly \SP\Storage\File\FileHandler $fileHandler;

    /**
     * XmlHandler constructor.
     */
    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * Cargar un archivo XML
     *
     * @param string $node
     *
     * @return XmlFileStorageInterface
     * @throws FileException
     * @throws RuntimeException
     */
    public function load($node = 'root'): self
    {
        $this->fileHandler->checkIsReadable();
        $this->fileHandler->getFileSize(true);

        $this->items = [];
        $this->setDOM();
        $this->Dom->load($this->fileHandler->getFile());

        $nodes = $this->Dom->getElementsByTagName($node);

        if ($nodes->length === 0) {
            throw new RuntimeException(__u('XML node does not exist'));
        }

        $this->items = $this->readChildNodes($nodes->item(0)->childNodes);

        return $this;
    }

    /**
     * Crear un nuevo documento XML
     */
    private function setDOM(): void
    {
        $this->Dom = new DOMDocument('1.0', 'utf-8');
    }

    /**
     * Leer de forma recursiva los nodos hijos y devolver un array multidimensional
     *
     */
    protected function readChildNodes(DOMNodeList $nodeList): array
    {
        $nodes = [];

        /** @var DOMElement $node */
        foreach ($nodeList as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                if (is_object($node->childNodes) && $node->childNodes->length > 1) {
                    if ($node->hasAttribute('multiple') && (int) $node->getAttribute('multiple') === 1) {
                        $nodes[] = $this->readChildNodes($node->childNodes);
                    } else {
                        $nodes[$node->nodeName] = $this->readChildNodes($node->childNodes);
                    }
                } else {
                    $val = is_numeric($node->nodeValue) && !str_contains((string) $node->nodeValue, '.') ? (int) $node->nodeValue : $node->nodeValue;

                    if ($node->nodeName === 'item') {
                        $nodes[] = $val;
                    } else {
                        $nodes[$node->nodeName] = $val;
                    }
                }
            }
        }

        return $nodes;
    }

    /**
     * Obtener un elemento del array
     *
     * @param $id
     */
    public function __get(string $id): mixed
    {
        return $this->items[$id];
    }

    /**
     * Guardar el archivo XML
     *
     * @param mixed  $data Data to be saved
     * @param string $node
     *
     * @return XmlFileStorageInterface
     * @throws FileException
     * @throws RuntimeException
     */
    public function save($data, $node = 'root'): self
    {
        $this->fileHandler->checkIsWritable();

        if (null === $data) {
            throw new RuntimeException(__u('There aren\'t any items to save'));
        }

        $this->setDOM();
        $this->Dom->formatOutput = true;

        $this->root = $this->Dom->createElement($node);
        $this->Dom->appendChild($this->root);
        $this->writeChildNodes($data, $this->root);

        $this->fileHandler->save($this->Dom->saveXML());

        return $this;
    }

    /**
     * Crear los nodos hijos recursivamente a partir de un array multidimensional
     *
     * @param mixed   $items
     */
    protected function writeChildNodes($items, DOMNode $Node, $type = null): void
    {
        foreach ($this->analyzeItems($items) as $key => $value) {
            if (is_int($key)) {
                $newNode = $this->Dom->createElement('item');
                $newNode->setAttribute('type', $type);
            } else {
                $newNode = $this->Dom->createElement($key);
            }

            if (is_array($value)) {
                $this->writeChildNodes($value, $newNode, $key);
            } elseif (is_object($value)) {
                $newNode->setAttribute('class', $value::class);
                $newNode->appendChild($this->Dom->createTextNode(base64_encode(serialize($value))));
            } else {
                $newNode->appendChild($this->Dom->createTextNode(trim((string) $value)));
            }

            $Node->appendChild($newNode);
        }
    }

    /**
     * Analizar el tipo de elementos
     *
     * @param mixed $items
     * @param bool  $serialize
     *
     * @return array
     */
    protected function analyzeItems($items, $serialize = false)
    {
        if (is_array($items)) {
            ksort($items);

            return $items;
        }

        if (is_object($items)) {
            return $serialize ? serialize($items) : $this->analyzeObject($items);
        }

        return [];
    }

    /**
     * Analizar un elemento del tipo objeto
     *
     * @param $object
     */
    protected function analyzeObject($object): array
    {
        $items = [];
        $Reflection = new ReflectionObject($object);

        foreach ($Reflection->getProperties() as $property) {
            $value = $property->getValue($object);

            if (is_bool($value)) {
                $items[$property->getName()] = (int) $value;
            } elseif (is_numeric($value) && !str_contains((string) $value, '.')) {
                $items[$property->getName()] = (int) $value;
            } else {
                $items[$property->getName()] = $value;
            }
        }

        ksort($items);

        return $items;
    }

    /**
     * Devolver los elementos cargados
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Establecer los elementos
     *
     * @param $items
     */
    public function setItems($items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param $path
     *
     * @return string
     * @throws FileException
     */
    public function getPathValue($path)
    {
        $this->fileHandler->checkIsReadable();
        $this->fileHandler->getFileSize(true);

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->load($this->fileHandler->getFile());

        $query = (new DOMXPath($dom))->query($path);

        if ($query->length === 0) {
            throw new RuntimeException(__u('XML node does not exist'));
        }

        return $query->item(0)->nodeValue;
    }

    public function getFileHandler(): FileHandler
    {
        return $this->fileHandler;
    }
}
