<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Definitions;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\StructureDefinitionInterface;

class ViewDefinition extends BaseDefinition implements StructureDefinitionInterface
{
    protected ?string $schema = null;

    /**
     * {@inheritDoc}
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * {@inheritDoc}
     */
    public function setSchema(?string $schema = null): self
    {
        $this->schema = $schema;

        return $this;
    }
}
