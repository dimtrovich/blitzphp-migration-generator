<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\GeneratorManagers;

use BlitzPHP\Debug\Timer;
use BlitzPHP\Utilities\Helpers;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\GeneratorManagerInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ViewDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\DependencyResolver;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;

abstract class BaseGeneratorManager implements GeneratorManagerInterface
{
    /**
     * @var TableDefinition[]
     */
    protected array $tables = [];

    /**
     * @var ViewDefinition[]
     */
    protected array $views = [];


    public function __construct(private Timer $timer)
    {
    }

    abstract public function init();

    public function createMissingDirectory(string $dirname)
    {
        if (! is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }
    }

    /**
     * @return TableDefinition[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @return ViewDefinition[]
     */
    public function getViews(): array
    {
        return $this->views;
    }

    public function addTable(TableDefinition $table): BaseGeneratorManager
    {
        $this->tables[] = $table;

        return $this;
    }

    public function addView(ViewDefinition $view): BaseGeneratorManager
    {
        $this->views[] = $view;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(string $basePath, array $tableNames = [], array $viewNames = []): array
    {
        $this->init();

        $tableDefinitions = Helpers::collect($this->getTables());
        $viewDefinitions = Helpers::collect($this->getViews());

        $this->createMissingDirectory($basePath);

        if (count($tableNames) > 0) {
            $tableDefinitions = $tableDefinitions->filter(function ($tableDefinition) use ($tableNames) {
                return in_array($tableDefinition->getName(), $tableNames);
            });
        }
        
        if (count($viewNames) > 0) {
            $viewDefinitions = $viewDefinitions->filter(function ($viewGenerator) use ($viewNames) {
                return in_array($viewGenerator->getName(), $viewNames);
            });
        }

        $tableDefinitions = $tableDefinitions->filter(function ($tableDefinition) {
            return ! $this->skipTable($tableDefinition->getName());
        });

        $viewDefinitions = $viewDefinitions->filter(function ($viewDefinition) {
            return ! $this->skipView($viewDefinition->getName());
        });

        $sorted = $this->sortTables($tableDefinitions->toArray());

        $tables = $this->writeTableMigrations($sorted, $basePath);

        $views = $this->writeViewMigrations($viewDefinitions->toArray(), $basePath, count($sorted));

        return compact('tables', 'views');
    }

    /**
     * @param TableDefinition[] $tables
     * 
     * @return TableDefinition[]
     */
    public function sortTables(array $tables): array
    {
        if (count($tables) <= 1) {
            return $tables;
        }

        if (ConfigResolver::get('sort_mode') == 'foreign_key') {
            return (new DependencyResolver($tables))->getDependencyOrder();
        }

        return $tables;
    }

    /**
     * @param TableDefinition[] $tables
     * 
     * @return array<string, array<string, string>>
     */
    public function writeTableMigrations(array $tables, string $basePath): array
    {
        $writted = [];

        foreach ($tables as $index => $table) {
            $this->timer->start($tableName = $table->getName());

            $path = $table->formatter()->write($basePath, $index);

            $writted[$tableName] = [
                'path' => $path,
                'time' => $this->timer->stop($tableName)->getElapsedTime($tableName),
            ];
        }

        return $writted;
    }

    /**
     * @param ViewDefinition[] $views
     * 
     * @return array<string, array<string, string>>
     */
    public function writeViewMigrations(array $views, string $basePath, int $tableCount = 0): array
    {
        $writted = [];

        foreach ($views as $key => $view) {
            $this->timer->start($viewName = $view->getName());

            $path = $view->formatter()->write($basePath, $tableCount + $key);

            $writted[$viewName] = [
                'path' => $path,
                'time' => $this->timer->stop($viewName)->getElapsedTime($viewName),
            ];
            
        }

        return $writted;
    }

    /**
     * @return array<string>
     */
    public function skippableTables(): array
    {
        return ConfigResolver::skippableTables(static::driver());
    }

    public function skipTable($table): bool
    {
        return in_array($table, $this->skippableTables());
    }

    /**
     * @return array<string>
     */
    public function skippableViews(): array
    {
        return ConfigResolver::skippableViews(static::driver());
    }

    public function skipView($view): bool
    {
        $skipViews = ConfigResolver::get('skip_views');
        if ($skipViews) {
            return true;
        }

        return in_array($view, $this->skippableViews());
    }
}
