<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Contracts;

use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ViewDefinition;

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
     * @return list<TableDefinition>
     */
    public function getTables(): array;

    /**
     * @return list<ViewDefinition>
     */
    public function getViews(): array;
}
