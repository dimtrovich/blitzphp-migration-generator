<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\MySQL;

use BlitzPHP\Utilities\String\Text;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Generators\TableGeneratorInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\BaseTableGenerator;
use Dimtrovich\BlitzPHP\MigrationGenerator\Tokenizers\MySQL\IndexTokenizer;
use Dimtrovich\BlitzPHP\MigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;

/**
 * Class TableGenerator
 * @package Dimtrovich\BlitzPHP\MigrationGenerator\Generators\MySQL
 */
class TableGenerator extends BaseTableGenerator implements TableGeneratorInterface
{
    public static function driver(): string
    {
        return 'mysql';
    }

    public function resolveStructure()
    {
        $structure = service('builder')->query('SHOW CREATE TABLE `' . $this->definition()->getName() . '`')->first(); //
        $structure = (array) $structure;
        if (isset($structure['Create Table'])) {
            $lines = explode("\n", $structure['Create Table']);

            array_shift($lines); //get rid of first line
            array_pop($lines); //get rid of last line

            $lines = array_map(fn ($item) => trim($item), $lines);
            $this->rows = $lines;
        } else {
            $this->rows = [];
        }
    }

    protected function isColumnLine($line)
    {
        return ! Text::startsWith($line, ['KEY', 'PRIMARY', 'UNIQUE', 'FULLTEXT', 'CONSTRAINT']);
    }

    public function parse()
    {
        foreach ($this->rows as $line) {
            if ($this->isColumnLine($line)) {
                $this->definition()->addColumn(ColumnTokenizer::parse($line)->definition());
            } else {
                $this->definition()->addIndex(IndexTokenizer::parse($line)->definition());
            }
        }
    }
}
