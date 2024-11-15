<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Helpers;

use BlitzPHP\Utilities\Helpers;
use MJS\TopSort\Implementations\FixedArraySort;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;

class DependencyResolver
{
    /** 
     * @var TableDefinition[]
     */
    protected array $sorted = [];

    /**
     * @param TableDefinition[] $tables
     */
    public function __construct(protected array $tables)
    {
        $this->build();
    }

    protected function build()
    {
        /** @var TableDefinition[] $keyedDefinitions */
        $keyedDefinitions = collect($this->tables)
            ->keyBy(fn (TableDefinition $table) => $table->getName());

        $dependencies = [];
        foreach ($this->tables as $table) {
            $dependencies[$table->getName()] = [];
        }

        foreach ($this->tables as $table) {
            foreach ($table->getForeignKeys() as $index) {
                if (! in_array($index->getForeignTable(), $dependencies[$table->getName()])) {
                    $dependencies[$table->getName()][] = $index->getForeignTable();
                }
            }
        }

        $sorter = new FixedArraySort();
        $circulars = [];
        $sorter->setCircularInterceptor(function ($nodes) use (&$circulars) {
            $circulars[] = [$nodes[count($nodes) - 2], $nodes[count($nodes) - 1]];
        });

        foreach ($dependencies as $table => $dependencyArray) {
            $sorter->add($table, $dependencyArray);
        }
        
        $sorted = $sorter->sort();
        $definitions = collect($sorted)->map(fn ($item) => $keyedDefinitions[$item])->toArray();

        foreach ($circulars as $groups) {
            [$start, $end] = $groups;
            
            $startDefinition = $keyedDefinitions[$start];
            $indicesForStart = collect($startDefinition->getForeignKeys())
                ->filter(fn (IndexDefinition $index) => $index->getForeignTable() == $end);

            foreach ($indicesForStart as $index) {
                $startDefinition->removeIndexDefinition($index);
            }

            if (! in_array($start, $sorted)) {
                $definitions[] = $startDefinition;
            }

            $endDefinition = $keyedDefinitions[$end];

            $indicesForEnd = collect($endDefinition->getForeignKeys())
                ->filter(fn (IndexDefinition $index) => $index->getForeignTable() === $start);

            foreach ($indicesForEnd as $index) {
                $endDefinition->removeIndexDefinition($index);
            }

            if (! in_array($end, $sorted)) {
                $definitions[] = $endDefinition;
            }

            $definitions[] = new TableDefinition([
                'name'    => $startDefinition->getName(),
                'driver'  => $startDefinition->getDriver(),
                'columns' => [],
                'indexes' => $indicesForStart->toArray()
            ]);

            $definitions[] = new TableDefinition([
                'name'    => $endDefinition->getName(),
                'driver'  => $endDefinition->getDriver(),
                'columns' => [],
                'indexes' => $indicesForEnd->toArray()
            ]);
        }
        
        $this->sorted = $definitions;
    }

    /**
     * @return TableDefinition[]
     */
    public function getDependencyOrder(): array
    {
        return $this->sorted;
    }
}
