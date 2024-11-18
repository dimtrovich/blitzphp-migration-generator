<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Generators;

use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;

interface TableGeneratorInterface
{
    public static function driver(): string;

    public function shouldResolveStructure(): bool;

    public function resolveStructure();

    public function parse();

    public function cleanUp();

    public function definition(): TableDefinition;
}
