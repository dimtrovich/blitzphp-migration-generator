<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Formatters;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\FormatterInterface;

class NullFormatter implements FormatterInterface
{
	/**
	 * {@inheritDoc}
	 */
    public function render(string $tabCharacter = '    '): string
    {
        return $tabCharacter;
    }

	/**
	 * {@inheritDoc}
	 */
    public function write(string $basePath, int $index = 0, string $tabCharacter = '    '): string
    {
        return $basePath;
    }
}
