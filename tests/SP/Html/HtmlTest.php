<?php
/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2022, Rubén Domínguez nuxsmin@$syspass.org
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
 * along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Tests\Html;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use SP\Html\Html;

/**
 * Class HtmlTest
 */
class HtmlTest extends TestCase
{
    private static $faker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$faker = Factory::create();
    }


    public function testGetSafeUrlOk()
    {
        $url = self::$faker->url;

        $this->assertEquals($url, Html::getSafeUrl($url));
    }

    /**
     * @return void
     */
    #[DataProvider('urlProvider')]
    public function testGetSafeUrlEncoded(string $url)
    {
        $this->assertEquals(0, preg_match('/["<>\']+/', Html::getSafeUrl($url)));
    }

    public static function urlProvider(): array
    {
        return [
            ['https://foo.com/<script>alert("TEST");</script>'],
            ['https://foo.com/><script>alert("TEST");</script>'],
            ['https://foo.com/"><script>alert("TEST");</script>'],
            ['https://foo.com/"%20onClick="alert(\'TEST\'")'],
            ['https://foo.com/" onClick="alert(\'TEST\')"'],
            ['mongodb+srv://cluster.foo.mongodb.net/bar'],
        ];
    }

    // CVE-2017-9306: entity-encoded tags must not decode into live markup
    public function testSanitizeStripsDecodedTags()
    {
        foreach (['&#60;img src=x onerror=alert(1)//', '&#x3C;svg/onload=alert(1)&#x3E;', '<svg/onload=alert(1)>report.pdf'] as $input) {
            $this->assertEquals(0, preg_match('/<[a-z!\/?]/i', (string) Html::sanitize($input)), $input);
        }

        $name = 'Rechnung März 2026 (final).pdf';
        $this->assertEquals($name, Html::sanitize($name));
    }
}
