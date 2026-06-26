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

use Faker\Factory;
use Faker\Generator;

/**
 * Base class for Faker-based test-data generators.
 *
 * Subclasses expose a `build<Entity>()` method returning a populated DataModel,
 * with every field filled by Faker so tests never depend on a database.
 *
 * Usage:
 *   $category = CategoryDataGenerator::factory()->buildCategoryData();
 */
abstract class DataGenerator
{
    protected Generator $faker;

    final private function __construct()
    {
        $this->faker = Factory::create();
    }

    public static function factory(): static
    {
        return new static();
    }
}
