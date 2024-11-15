<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Definitions;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\StructureDefinitionInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ValueToString;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\WritableTrait;

class IndexDefinition extends BaseDefinition implements StructureDefinitionInterface
{
    const TYPE_FOREIGN = 'foreign';
    const TYPE_PRIMARY = 'primary';
    const TYPE_UNIQUE = 'unique';

    use WritableTrait;

    protected string $type;

    protected array $columns = [];

    protected array $foreignColumns = [];

    protected string $foreignTable;

    protected array $constraintActions = [];

    public function isForeignKey(): bool
    {
        return $this->getType() === self::TYPE_FOREIGN;
    }

    public function isPrimaryKey(): bool
    {
        return $this->getType() === self::TYPE_PRIMARY;
    }

    public function isUniqueKey(): bool
    {
        return $this->getType() === self::TYPE_UNIQUE;
    }

    public function isMultiColumnIndex(): bool
    {
        return count($this->columns) > 1;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getForeignColumns(): array
    {
        return $this->foreignColumns;
    }

    public function getForeignTable(): string
    {
        return $this->foreignTable;
    }

    public function getConstraintActions(): array
    {
        return $this->constraintActions;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function setForeignColumns(array $columns): self
    {
        $this->foreignColumns = $columns;

        return $this;
    }

    public function setForeignTable(string $table): self
    {
        $this->foreignTable = $table;

        return $this;
    }

    public function setConstraintActions(array $constraintActions): self
    {
        $this->constraintActions = $constraintActions;

        return $this;
    }

    public function render(): string
    {
        if ($this->isForeignKey()) {
            $indexName = '';
            if (ConfigResolver::get('definitions.use_defined_foreign_key_index_names')) {
                $indexName = ', \'' . $this->getName() . '\'';
            }

            $base = '$table->foreign(' . ValueToString::make($this->columns, true) . $indexName . ')->references(' . ValueToString::make($this->foreignColumns, true) . ')->on(' . ValueToString::make($this->foreignTable) . ')';
            foreach ($this->constraintActions as $type => $action) {
                $base .= '->on' . ucfirst($type) . '(' . ValueToString::make($action) . ')';
            }

            return $base;
        } 
        
        if ($this->isPrimaryKey()) {
            $indexName = '';
            if (ConfigResolver::get('definitions.use_defined_primary_key_index_names') && $this->getName() !== null) {
                $indexName = ', \'' . $this->getName() . '\'';
            }

            return '$table->primary(' . ValueToString::make($this->columns) . $indexName . ')';
        }

        if ($this->isUniqueKey()) {
            $indexName = '';
            if (ConfigResolver::get('definitions.use_defined_unique_key_index_names')) {
                $indexName = ', \'' . $this->getName() . '\'';
            }

            return '$table->unique(' . ValueToString::make($this->columns) . $indexName . ')';
        } 

        if ($this->type === 'index') {
            $indexName = '';
            if (ConfigResolver::get('definitions.use_defined_index_names')) {
                $indexName = ', \'' . $this->getName() . '\'';
            }

            return '$table->index(' . ValueToString::make($this->columns) . $indexName . ')';
        }

        return '';
    }
}
