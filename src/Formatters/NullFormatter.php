<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
