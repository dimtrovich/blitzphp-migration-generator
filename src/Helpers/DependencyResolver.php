<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Helpers;

use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;
use MJS\TopSort\Implementations\FixedArraySort;

class DependencyResolver
{
    /**
     * @var list<TableDefinition>
     */
    protected array $sorted = [];

    /**
     * @param list<TableDefinition> $tables
     */
    public function __construct(protected array $tables)
    {
        $this->build();
    }

    protected function build()
    {
        /** @var list<TableDefinition> $keyedDefinitions */
        $keyedDefinitions = collect($this->tables)
            ->keyBy(fn (TableDefinition $table) => $table->getName());

        $dependencies = [];

        foreach ($this->tables as $table) {
            $dependencies[$table->getName()] = [];
        }

        foreach ($this->tables as $table) {
            foreach ($table->getForeignKeys() as $index) {
                if (! in_array($index->getForeignTable(), $dependencies[$table->getName()], true)) {
                    $dependencies[$table->getName()][] = $index->getForeignTable();
                }
            }
        }

        $sorter    = new FixedArraySort();
        $circulars = [];
        $sorter->setCircularInterceptor(function ($nodes) use (&$circulars) {
            $circulars[] = [$nodes[count($nodes) - 2], $nodes[count($nodes) - 1]];
        });

        foreach ($dependencies as $table => $dependencyArray) {
            $sorter->add($table, $dependencyArray);
        }

        $sorted      = $sorter->sort();
        $definitions = collect($sorted)->map(fn ($item) => $keyedDefinitions[$item])->toArray();

        foreach ($circulars as $groups) {
            [$start, $end] = $groups;

            $startDefinition = $keyedDefinitions[$start];
            $indicesForStart = collect($startDefinition->getForeignKeys())
                ->filter(fn (IndexDefinition $index) => $index->getForeignTable() === $end);

            foreach ($indicesForStart as $index) {
                $startDefinition->removeIndexDefinition($index);
            }

            if (! in_array($start, $sorted, true)) {
                $definitions[] = $startDefinition;
            }

            $endDefinition = $keyedDefinitions[$end];

            $indicesForEnd = collect($endDefinition->getForeignKeys())
                ->filter(fn (IndexDefinition $index) => $index->getForeignTable() === $start);

            foreach ($indicesForEnd as $index) {
                $endDefinition->removeIndexDefinition($index);
            }

            if (! in_array($end, $sorted, true)) {
                $definitions[] = $endDefinition;
            }

            $definitions[] = new TableDefinition([
                'name'    => $startDefinition->getName(),
                'driver'  => $startDefinition->getDriver(),
                'columns' => [],
                'indexes' => $indicesForStart->toArray(),
            ]);

            $definitions[] = new TableDefinition([
                'name'    => $endDefinition->getName(),
                'driver'  => $endDefinition->getDriver(),
                'columns' => [],
                'indexes' => $indicesForEnd->toArray(),
            ]);
        }

        $this->sorted = $definitions;
    }

    /**
     * @return list<TableDefinition>
     */
    public function getDependencyOrder(): array
    {
        return $this->sorted;
    }
}
