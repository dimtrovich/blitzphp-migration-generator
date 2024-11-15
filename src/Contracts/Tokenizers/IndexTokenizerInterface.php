<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Tokenizers;

use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition;

interface IndexTokenizerInterface extends TokenizerInterface
{
    public function definition(): IndexDefinition;
}
