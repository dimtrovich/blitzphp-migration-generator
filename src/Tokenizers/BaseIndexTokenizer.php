<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Tokenizers;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Tokenizers\IndexTokenizerInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition;

abstract class BaseIndexTokenizer extends BaseTokenizer implements IndexTokenizerInterface
{
    protected IndexDefinition $definition;

    public function __construct(string $value)
    {
        $this->definition = new IndexDefinition();
        parent::__construct($value);
    }

    public function definition(): IndexDefinition
    {
        return $this->definition;
    }
}
