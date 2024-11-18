<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns;

trait WritesToFile
{
    public function write(string $basePath, int $index = 0, string $tabCharacter = '    '): void
    {
        if (method_exists($this, 'isWritable') && ! $this->isWritable()) {
            return;
        }

        $stub = $this->generateStub($tabCharacter);

        $fileName = $this->getStubFileName($index);

        file_put_contents($basePath . '/' . $fileName, $stub);
    }
}
