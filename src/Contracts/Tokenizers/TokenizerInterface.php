<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Tokenizers;

interface TokenizerInterface
{
    public function tokenize(): static;
}
