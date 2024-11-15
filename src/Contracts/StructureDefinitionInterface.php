<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts;

interface StructureDefinitionInterface
{
    public function getDriver(): string;

    public function setDriver(string $driver): self;

    public function getName(): ?string;

    public function setName(?string $name): self;

    public function formatter(): FormatterInterface;
}