<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns;

use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\BaseTableGenerator;

/**
 * @mixin BaseTableGenerator
 */
trait CleansUpColumnIndices
{
    protected function cleanUpColumnsWithIndices(): void
    {
        foreach ($this->definition()->getIndexes() as &$index) {
            /** @var \Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition $index */
            if (! $index->isWritable()) {
                continue;
            }
            $columns = $index->getColumns();

            foreach ($columns as $indexColumn) {
                foreach ($this->definition()->getColumns() as $column) {
                    /** @var \Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ColumnDefinition $column */
                    if ($column->getName() === $indexColumn) {
                        $indexType = $index->getType();
                        $isMultiColumnIndex = $index->isMultiColumnIndex();

                        if ($index->isPrimaryKey() && ! $isMultiColumnIndex) {
                            $column->setPrimary(true)->addIndex($index);
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'index' && ! $isMultiColumnIndex) {
                            $isForeignKeyIndex = false;
                            foreach ($this->definition()->getIndexes() as $innerIndex) {
                                /** @var \Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition $innerIndex */
                                if ($innerIndex->isForeignKey() && ! $innerIndex->isMultiColumnIndex() && $innerIndex->getColumns()[0] == $column->getName()) {
                                    $isForeignKeyIndex = true;

                                    break;
                                }
                            }
                            if ($isForeignKeyIndex === false) {
                                $column->setIndex(true)->addIndex($index);
                            }
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'unique' && ! $isMultiColumnIndex) {
                            $column->setUnique(true)->addIndex($index);
                            $index->markAsWritable(false);
                        }
                    }
                }
            }
        }
    }
}
