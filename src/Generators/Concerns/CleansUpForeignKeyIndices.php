<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns;

use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\BaseTableGenerator;

/**
 * @mixin BaseTableGenerator
 */
trait CleansUpForeignKeyIndices
{
    protected function cleanUpForeignKeyIndices(): void
    {
        $indexDefinitions = $this->definition()->getIndexes();

        foreach ($indexDefinitions as $index) {
            /** @var \Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition $index */
            if ($index->getType() === 'index') {
                // look for corresponding foreign key for this index
                $columns   = $index->getColumns();
                $indexName = $index->getName();

                foreach ($indexDefinitions as $innerIndex) {
                    /** @var \Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition $innerIndex */
                    if ($innerIndex->getName() !== $indexName) {
                        if ($innerIndex->isForeignKey()) {
                            $cols = $innerIndex->getColumns();
                            if (count(array_intersect($columns, $cols)) === count($columns)) {
                                // has same columns
                                $index->markAsWritable(false);

                                break;
                            }
                        }
                    }
                }
            }
        }
    }
}
