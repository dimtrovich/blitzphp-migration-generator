<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Definitions;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\FormatterInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\StructureDefinitionInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Formatters\NullFormatter;

abstract class BaseDefinition implements StructureDefinitionInterface
{
    protected ?string $name = null; // les clés primaires n'ont généralement pas de nom

    protected string $driver = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * {@inheritDoc}
     */
    public function setDriver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function formatter(): FormatterInterface
    {
        $class = str_replace('Definition', 'Formatter', static::class);

        if (class_exists($class)) {
            return new $class($this);
        }

        return new NullFormatter($this);
    }
}
