<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts;

use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ViewDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;

interface GeneratorManagerInterface
{
    public static function driver(): string;

    /**
     * @return array{tables: array, views: array}
     */
    public function handle(string $basePath, array $tableNames = [], array $viewNames = []): array;

    public function addTable(TableDefinition $table);

    public function addView(ViewDefinition $table);

    /**
     * @return TableDefinition[]
     */
    public function getTables(): array;

    /**
     * @return ViewDefinition[]
     */
    public function getViews(): array;
}
