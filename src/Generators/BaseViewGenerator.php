<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Generators\ViewGeneratorInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ViewDefinition;

abstract class BaseViewGenerator implements ViewGeneratorInterface
{
    protected ViewDefinition $definition;

    public function __construct(string $viewName, ?string $schema = null)
    {
        $this->definition = new ViewDefinition([
            'driver' => static::driver(),
            'name'   => $viewName,
            'schema' => $schema
        ]);
    }

    public function definition(): ViewDefinition
    {
        return $this->definition;
    }

    public static function init(string $viewName, ?string $schema = null): static
    {
        $instance = new static($viewName, $schema);
        
        if ($schema === null) {
            $instance->resolveSchema();
        }
        $instance->parse();

        return $instance;
    }
}
