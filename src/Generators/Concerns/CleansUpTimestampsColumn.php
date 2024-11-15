<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns;

use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\BaseTableGenerator;

/**
 * @mixin BaseTableGenerator
 */
trait CleansUpTimestampsColumn
{
    protected function cleanUpTimestampsColumn(): void
    {
        $timestampColumns = [];
        foreach ($this->definition()->getColumns() as &$column) {
            /** @var \Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ColumnDefinition $column */

            $columnName = $column->getName();
            if ($columnName === 'created_at') {
                $timestampColumns['created_at'] = $column;
            } elseif ($columnName === 'updated_at') {
                $timestampColumns['updated_at'] = $column;
            }
            if (count($timestampColumns) === 2) {
                foreach ($timestampColumns as $timestampColumn) {
                    if ($timestampColumn->useCurrent() || $timestampColumn->useCurrentOnUpdate()) {
                        //don't convert to a `timestamps()` method if useCurrent is used

                        return;
                    }
                }
                $timestampColumns['created_at']
                    ->setName(null)
                    ->setMethodName('timestamps')
                    ->setNullable(false);
                    
                $timestampColumns['updated_at']->markAsWritable(false);

                break;
            }
        }
    }
}
