<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns;

use BlitzPHP\Utilities\String\Text;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ColumnDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\BaseTableGenerator;

/**
 * @mixin BaseTableGenerator
 */
trait CleansUpMorphColumns
{
    protected function cleanUpMorphColumns(): void
    {
        $morphColumns = [];

        foreach ($this->definition()->getColumns() as &$column) {
            /** @var ColumnDefinition $column */

            if (Text::endsWith($columnName = $column->getName(), ['_id', '_type'])) {
                $pieces = explode('_', $columnName);
                $type = array_pop($pieces); //pop off id or type
                $morphColumn = implode('_', $pieces);
                $morphColumns[$morphColumn][$type] = $column;
            }
        }

        foreach ($morphColumns as $columnName => $fields) {
            if (count($fields) === 2) {
                /** @var ColumnDefinition $idField */
                $idField = $fields['id'];
                /** @var ColumnDefinition $typeField */
                $typeField = $fields['type'];

                if (! ($idField->isUUID() || Text::contains($idField->getMethodName(), 'integer'))) {
                    //should only be a uuid field or integer
                    continue;
                }
                if ($typeField->getMethodName() != 'string') {
                    //should only be a string field
                    continue;
                }

                if ($idField->isUUID()) {
                    //UUID morph
                    $idField
                        ->setMethodName('uuidMorphs')
                        ->setMethodParameters([])
                        ->setName($columnName);
                } else {
                    //regular morph
                    $idField
                        ->setMethodName('morphs')
                        ->setName($columnName);
                }
                $typeField->markAsWritable(false);

                foreach ($this->definition->getIndexDefinitions() as $index) {
                    $columns = $index->getIndexColumns();
                    $morphColumns = [$columnName . '_id', $columnName . '_type'];

                    if (count($columns) == count($morphColumns) && array_diff($columns, $morphColumns) === array_diff($morphColumns, $columns)) {
                        $index->markAsWritable(false);

                        break;
                    }
                }
            }
        }
    }
}
