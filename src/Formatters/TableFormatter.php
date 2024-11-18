<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Formatters;

use BlitzPHP\Utilities\Date;
use BlitzPHP\Utilities\Helpers;
use BlitzPHP\Utilities\String\Text;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\FormatterInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ColumnDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\IndexDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\Formatter;

class TableFormatter implements FormatterInterface
{
    public function __construct(private TableDefinition $table)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render(string $tabCharacter = '    '): string
    {
        $tableName = $this->table->getPresentableName();

        $schema = $this->getSchema($tabCharacter);
        $stub   = file_get_contents($this->getStubPath());

        if (str_contains($stub, '[TableUp]')) {
            // uses new syntax
            $stub = Formatter::replace($tabCharacter, '[TableUp]', $this->stubTableUp($tabCharacter), $stub);
            $stub = Formatter::replace($tabCharacter, '[TableDown]', $this->stubTableDown($tabCharacter), $stub);
        }

        $stub = str_replace('[TableName:Studly]', Text::studly($tableName), $stub);
        $stub = str_replace('[TableName]', $tableName, $stub);

        return Formatter::replace($tabCharacter, '[Schema]', $schema, $stub);
    }

    public function getStubFileName(int $index = 0): string
    {
        $driver           = $this->table->getDriver();
        $baseStubFileName = ConfigResolver::tableNamingScheme($driver);

        foreach ($this->stubNameVariables($index) as $variable => $replacement) {
            if (preg_match('/\\[' . $variable . '\\]/i', $baseStubFileName) === 1) {
                $baseStubFileName = preg_replace('/\\[' . $variable . '\\]/i', $replacement, $baseStubFileName);
            }
        }

        return $baseStubFileName;
    }

    public function getStubPath(): string
    {
        return ConfigResolver::stub('table', $this->table->getDriver());
    }

    public function getStubCreatePath(): string
    {
        return ConfigResolver::stub('table-create', $this->table->getDriver());
    }

    public function getStubModifyPath(): string
    {
        return ConfigResolver::stub('table-modify', $this->table->getDriver());
    }

    public function stubNameVariables(int $index): array
    {
        $tableName = $this->table->getPresentableName();

        return [
            'TableName:Studly'      => Text::studly($tableName),
            'TableName:Lowercase'   => strtolower($tableName),
            'TableName'             => $tableName,
            'Timestamp'             => Date::now()->format($timestampFormat = config('migrations.timestampFormat', 'Y-m-d-His_')),
            'Index'                 => (string) $index,
            'IndexedEmptyTimestamp' => '0000-00-00_' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            'IndexedTimestamp'      => Date::now()->addSeconds($index)->format($timestampFormat),
        ];
    }

    public function getSchema(string $tab = ''): string
    {
        $formatter = new Formatter($tab);
        Helpers::collect($this->table->getColumns())
            ->filter(fn (ColumnDefinition $col) => $col->isWritable())
            ->each(function (ColumnDefinition $column) use ($formatter) {
                $formatter->line($column->render() . ';');
            });

        $indices = Helpers::collect($this->table->getIndexes())
            ->filter(fn (IndexDefinition $index) => $index->isWritable());

        if ($indices->count() > 0) {
            if (count($this->table->getColumns()) > 0) {
                $formatter->line('');
            }
            $indices->each(function (IndexDefinition $index) use ($formatter) {
                $formatter->line($index->render() . ';');
            });
        }

        return $formatter->render();
    }

    public function stubTableUp(string $tab = '', array $variables = []): string
    {
        if ($variables === []) {
            $variables = $this->getStubVariables($tab);
        }

        if (count($this->table->getColumns()) === 0) {
            $tableModifyStub = file_get_contents($this->getStubModifyPath());

            foreach ($variables as $var => $replacement) {
                $tableModifyStub = Formatter::replace($tab, '[' . $var . ']', $replacement, $tableModifyStub);
            }

            return $tableModifyStub;
        }

        $tableUpStub = file_get_contents($this->getStubCreatePath());

        foreach ($variables as $var => $replacement) {
            $tableUpStub = Formatter::replace($tab, '[' . $var . ']', $replacement, $tableUpStub);
        }

        return $tableUpStub;
    }

    public function stubTableDown($tab = ''): string
    {
        if (count($this->table->getColumns()) === 0) {
            $schema = '$this->modify(\'' . $this->table->getName() . '\', function(Structure $table) {' . "\n";

            foreach ($this->table->getForeignKeys() as $index) {
                $schema .= $tab . '$table->dropForeign(\'' . $index->getName() . '\');' . "\n";
            }

            return $schema . '});';
        }

        return '$this->dropIfExists(\'' . $this->table->getName() . '\');';
    }

    protected function getStubVariables($tab = ''): array
    {
        $tableName = $this->table->getName();

        return [
            'TableName:Studly'    => Text::studly($tableName),
            'TableName:Lowercase' => strtolower($tableName),
            'TableName'           => $tableName,
            'Schema'              => $this->getSchema($tab),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $basePath, int $index = 0, string $tabCharacter = '    '): string
    {
        $stub = $this->render($tabCharacter);

        $fileName = $this->getStubFileName($index);
        file_put_contents($final = $basePath . '/' . $fileName, $stub);

        return $final;
    }
}
