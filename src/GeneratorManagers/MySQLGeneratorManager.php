<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\GeneratorManagers;

use BlitzPHP\Utilities\String\Text;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\GeneratorManagerInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\MySQL\TableGenerator;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\MySQL\ViewGenerator;

class MySQLGeneratorManager extends BaseGeneratorManager implements GeneratorManagerInterface
{
    public static function driver(): string
    {
        return 'mysql';
    }

    public function init()
    {
        $tables = service('builder')->query('SHOW FULL TABLES')->all();

        foreach ($tables as $rowNumber => $table) {
            $tableData = (array) $table;
            $table     = $tableData[array_key_first($tableData)];
            $tableType = $tableData['Table_type'];
            if ($tableType === 'BASE TABLE') {
                $this->addTable(TableGenerator::init($table)->definition());
            } elseif ($tableType === 'VIEW') {
                $this->addView(ViewGenerator::init($table)->definition());
            }
        }
    }

    public function addTable(TableDefinition $table): BaseGeneratorManager
    {
        $prefix = Service('database')->prefix;
        if (! empty($prefix) && Text::startsWith($table->getName(), $prefix)) {
            $table->setName(Text::replaceFirst($prefix, '', $table->getName()));
        }

        return parent::addTable($table);
    }
}
