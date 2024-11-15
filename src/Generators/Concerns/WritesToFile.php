<?php

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
