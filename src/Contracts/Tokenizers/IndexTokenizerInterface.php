<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Tokenizers;

use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition;

interface IndexTokenizerInterface extends TokenizerInterface
{
    public function definition(): IndexDefinition;
}
