<?php
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
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

namespace SP\Tests\SP\Core\Crypt;

use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use phpseclib3\Crypt\RSA;
use PHPUnit\Framework\TestCase;
use SP\Core\Crypt\CryptPKI;
use SP\Core\Exceptions\SPException;
use SP\Storage\File\FileException;
use SP\Util\PasswordUtil;

/**
 * Class CryptPKITest
 *
 * @package SP\Tests\SP\Core\Crypt
 */
class CryptPKITest extends TestCase
{
    /**
     * @var CryptPKI
     */
    private $cryptPki;

    /**
     * @throws EnvironmentIsBrokenException
     * @throws FileException
     */
    public function testDecryptRSA()
    {
        // generateRandomBytes() returns hex (2 chars per byte), so cap at the RSA payload limit
        $maxSize = CryptPKI::getMaxDataSize();

        $random = substr(PasswordUtil::generateRandomBytes($maxSize), 0, $maxSize);

        $data = $this->cryptPki->encryptRSA($random);

        $this->assertNotEmpty($data);

        $this->assertEquals($random, $this->cryptPki->decryptRSA($data));

        $this->assertFalse($this->cryptPki->decryptRSA('test123'));
    }

    /**
     * @throws FileException
     */
    public function testDecryptRSAPassword()
    {
        $length = (CryptPKI::KEY_SIZE / 8) - 11;

        $random = PasswordUtil::randomPassword($length);

        $data = $this->cryptPki->encryptRSA($random);

        $this->assertNotEmpty($data);

        $this->assertEquals($random, $this->cryptPki->decryptRSA($data));
    }

    /**
     * @throws EnvironmentIsBrokenException
     * @throws FileException
     */
    public function testEncryptRSARejectsOversizedData()
    {
        $random = PasswordUtil::generateRandomBytes(CryptPKI::getMaxDataSize() + 1);

        $this->expectException(\LengthException::class);

        $this->cryptPki->encryptRSA($random);
    }

    /**
     * @throws FileException
     */
    public function testGetPublicKey()
    {
        $key = $this->cryptPki->getPublicKey();

        $this->assertNotEmpty($key);

        $this->assertMatchesRegularExpression('/^-----BEGIN PUBLIC KEY-----.*/', $key);
    }

    /**
     * @throws FileException
     */
    public function testGetPrivateKey()
    {
        $key = $this->cryptPki->getPrivateKey();

        $this->assertNotEmpty($key);

        // phpseclib 3 uses PKCS#8 format (BEGIN PRIVATE KEY) instead of PKCS#1 (BEGIN RSA PRIVATE KEY)
        $this->assertMatchesRegularExpression('/^-----BEGIN (RSA )?PRIVATE KEY-----.*/', $key);
    }

    /**
     * @throws EnvironmentIsBrokenException
     * @throws FileException
     */
    public function testEncryptRSA()
    {
        $maxSize = CryptPKI::getMaxDataSize();

        $random = substr(PasswordUtil::generateRandomBytes($maxSize), 0, $maxSize);

        $data = $this->cryptPki->encryptRSA($random);

        $this->assertNotEmpty($data);

        $this->assertEquals($random, $this->cryptPki->decryptRSA($data));
    }

    /**
     * @throws SPException
     */
    public function testCreateKeys()
    {
        $this->cryptPki->createKeys();

        $this->assertFileExists(CryptPKI::PUBLIC_KEY_FILE);
        $this->assertFileExists(CryptPKI::PRIVATE_KEY_FILE);
    }

    /**
     * @throws FileException
     */
    public function testGetKeySize()
    {
        $this->assertEquals(CryptPKI::KEY_SIZE, $this->cryptPki->getKeySize());
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @throws SPException
     */
    protected function setUp(): void
    {
        $this->cryptPki = new CryptPKI();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        unlink(CryptPKI::PUBLIC_KEY_FILE);
        unlink(CryptPKI::PRIVATE_KEY_FILE);
    }


}
