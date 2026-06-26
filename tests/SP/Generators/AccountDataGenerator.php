<?php

declare(strict_types=1);
/*
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2024, Rubén Domínguez nuxsmin@$syspass.org
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

namespace SP\Tests\Generators;

use SP\DataModel\AccountData;

/**
 * Builds a populated AccountData (core scalar fields) with Faker values (no DB needed).
 */
final class AccountDataGenerator extends DataGenerator
{
    public function buildAccountData(): AccountData
    {
        $account = new AccountData($this->faker->randomNumber(3));
        $account->setUserId($this->faker->randomNumber(3));
        $account->setUserGroupId($this->faker->randomNumber(3));
        $account->setName($this->faker->words(3, true));
        $account->setClientId($this->faker->randomNumber(3));
        $account->setCategoryId($this->faker->randomNumber(3));
        $account->setLogin($this->faker->userName());
        $account->setUrl($this->faker->url());
        $account->setNotes($this->faker->sentence());

        return $account;
    }
}
