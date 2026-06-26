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

namespace SP\Core\Crypt;

defined('APP_ROOT') || die();

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PrivateKey;
use SP\Core\Exceptions\SPException;
use SP\Storage\File\FileException;
use SP\Storage\File\FileHandler;

/**
 * Class CryptPKI para el manejo de las funciones para PKI
 *
 * @package SP
 */
final class CryptPKI
{
    public const KEY_SIZE = 1024;

    public const PUBLIC_KEY_FILE = CONFIG_PATH . DIRECTORY_SEPARATOR . 'pubkey.pem';

    public const PRIVATE_KEY_FILE = CONFIG_PATH . DIRECTORY_SEPARATOR . 'key.pem';

    /**
     * @var FileHandler
     */
    private $publicKeyFile;

    /**
     * @var FileHandler
     */
    private $privateKeyFile;

    /**
     * @throws SPException
     */
    public function __construct()
    {
        $this->setUp();
    }

    /**
     * Check if private and public keys exist
     *
     * @throws SPException
     */
    private function setUp()
    {
        $this->publicKeyFile = new FileHandler(self::PUBLIC_KEY_FILE);
        $this->privateKeyFile = new FileHandler(self::PRIVATE_KEY_FILE);

        try {
            $this->publicKeyFile->checkFileExists();
            $this->privateKeyFile->checkFileExists();
        } catch (FileException $e) {
            processException($e);

            $this->createKeys();
        }
    }

    /**
     * Crea el par de claves pública y privada
     *
     * @throws FileException
     */
    public function createKeys()
    {
        $privateKey = RSA::createKey(self::KEY_SIZE);
        $publicKey = $privateKey->getPublicKey();

        $this->publicKeyFile->save((string) $publicKey);
        $this->privateKeyFile->save((string) $privateKey);

        chmod(self::PRIVATE_KEY_FILE, 0600);
    }

    /**
     * @return int
     */
    public static function getMaxDataSize()
    {
        return (self::KEY_SIZE / 8) - 11;
    }

    /**
     * Encriptar datos con la clave pública
     *
     * @param string $data los datos a encriptar
     *
     * @return string
     * @throws FileException
     */
    public function encryptRSA($data)
    {
        $publicKey = RSA::loadPublicKey($this->getPublicKey())
            ->withPadding(RSA::ENCRYPTION_PKCS1);

        return $publicKey->encrypt($data);
    }

    /**
     * Devuelve la clave pública desde el archivo
     *
     * @return string
     * @throws FileException
     */
    public function getPublicKey()
    {
        return $this->publicKeyFile
            ->checkFileExists()
            ->readToString();
    }

    /**
     * Desencriptar datos cifrados con la clave pública
     *
     * @param string $data los datos a desencriptar
     *
     * @return string|false clear data, or false if $data is not valid RSA ciphertext
     * @throws FileException
     */
    public function decryptRSA($data)
    {
        $privateKey = RSA::loadPrivateKey($this->getPrivateKey())
            ->withPadding(RSA::ENCRYPTION_PKCS1);

        try {
            return $privateKey->decrypt($data);
        } catch (\Exception $e) {
            // phpseclib3 throws on malformed ciphertext (e.g. attacker-supplied
            // request data); callers expect false on failure, not an exception.
            return false;
        }
    }

    /**
     * Devuelve la clave privada desde el archivo
     *
     * @return string
     * @throws FileException
     */
    public function getPrivateKey()
    {
        return $this->privateKeyFile
            ->checkFileExists()
            ->readToString();
    }

    /**
     * @return int
     * @throws FileException
     */
    public function getKeySize()
    {
        $privateKey = RSA::loadPrivateKey($this->getPrivateKey());

        return $privateKey->getLength();
    }
}
