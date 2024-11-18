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

class TableDefinition extends BaseDefinition implements StructureDefinitionInterface
{
    /**
     * @var list<ColumnDefinition>
     */
    protected array $columns = [];

    /**
     * @var list<IndexDefinition>
     */
    protected array $indexes = [];

    public function getPresentableName(): string
    {
        if (count($this->getColumns()) === 0) {
            if (count($definitions = $this->getIndexes()) > 0) {
                $first = collect($definitions)->first();

                // une table à clé étrangère uniquement à partir de la résolution des dépendances
                return $this->getName() . '_' . $first->getName();
            }
        }

        return $this->getName();
    }

    /**
     * @return list<ColumnDefinition>.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param list<ColumnDefinition> $columns
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function addColumn(ColumnDefinition $definition): self
    {
        $this->columns[] = $definition;

        return $this;
    }

    /**
     * @return list<IndexDefinition>
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @return list<IndexDefinition>
     */
    public function getForeignKeys(): array
    {
        return collect($this->getIndexes())
            ->filter(fn (IndexDefinition $index) => $index->isForeignKey())
            ->toArray();
    }

    /**
     * @param list<IndexDefinition> $indexDefinitions
     */
    public function setIndexes(array $indexes): self
    {
        $this->indexes = $indexes;

        return $this;
    }

    public function addIndex(IndexDefinition $definition): self
    {
        $this->indexes[] = $definition;

        return $this;
    }

    public function removeIndexDefinition(IndexDefinition $definition): self
    {
        foreach ($this->indexes as $key => $index) {
            if ($definition->getName() === $index->getName()) {
                unset($this->indexes[$key]);

                break;
            }
        }

        return $this;
    }

    /**
     * @return list<ColumnDefinition>
     */
    public function getPrimaryKey(): array
    {
        return collect($this->getColumns())
            ->filter(fn (ColumnDefinition $column) => $column->isPrimary())
            ->toArray();
    }
}
