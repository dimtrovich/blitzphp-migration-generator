<?php

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
