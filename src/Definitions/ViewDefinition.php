<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Definitions;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\StructureDefinitionInterface;

class ViewDefinition extends BaseDefinition implements StructureDefinitionInterface
{
    protected ?string $schema = null;

    /**
     * {@inheritDoc}
     */
    public function getSchema(): ?string
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
