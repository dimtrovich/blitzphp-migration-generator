<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Generators;

interface ViewGeneratorInterface
{
    public static function driver(): string;

    public function parse();

    public function resolveSchema();
}
