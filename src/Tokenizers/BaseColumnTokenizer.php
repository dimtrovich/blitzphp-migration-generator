<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Tokenizers;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Tokenizers\ColumnTokenizerInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ColumnDefinition;

abstract class BaseColumnTokenizer extends BaseTokenizer implements ColumnTokenizerInterface
{
    protected ColumnDefinition $definition;

    public function __construct(string $value)
    {
        $this->definition = new ColumnDefinition();
        parent::__construct($value);
    }

    public function definition(): ColumnDefinition
    {
        return $this->definition;
    }
}
