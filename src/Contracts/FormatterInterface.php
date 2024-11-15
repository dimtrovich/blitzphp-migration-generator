<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts;

interface FormatterInterface
{

    public function render(string $tabCharacter = '    '): string;

    public function write(string $basePath, int $index = 0, string $tabCharacter = '    '): string;
}
