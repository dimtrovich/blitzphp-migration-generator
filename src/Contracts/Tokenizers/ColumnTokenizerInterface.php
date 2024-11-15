<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Tokenizers;

use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ColumnDefinition;

interface ColumnTokenizerInterface extends TokenizerInterface
{
    public function definition(): ColumnDefinition;
}
