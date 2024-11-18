<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts;

interface StructureDefinitionInterface
{
    public function getDriver(): string;

    public function setDriver(string $driver): self;

    public function getName(): ?string;

    public function setName(?string $name): self;

    public function formatter(): FormatterInterface;
}
