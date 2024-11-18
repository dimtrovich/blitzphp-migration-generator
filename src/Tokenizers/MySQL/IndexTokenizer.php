<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Tokenizers\MySQL;

use Dimtrovich\BlitzPHP\MigrationGenerator\Tokenizers\BaseIndexTokenizer;

class IndexTokenizer extends BaseIndexTokenizer
{
    public function tokenize(): static
    {
        $this->consumeType();

        if (! $this->definition->isPrimaryKey()) {
            $this->consumeName();
        }

        if ($this->definition->isForeignKey()) {
            $this->consumeForeignKey();
        } else {
            $this->consumeColumns();
        }

        return $this;
    }

    private function consumeType()
    {
        $piece = $this->consume();
        $upper = strtoupper($piece);

        if (in_array($upper, ['PRIMARY', 'UNIQUE', 'FULLTEXT'], true)) {
            $this->definition->setType(strtolower($piece));
            $this->consume(); // just the word KEY
        } elseif ($upper === 'KEY') {
            $this->definition->setType('index');
        } elseif ($upper === 'CONSTRAINT') {
            $this->definition->setType('foreign');
        }
    }

    private function consumeName()
    {
        $piece = $this->consume();
        $this->definition->setName($this->parseColumn($piece));
    }

    private function consumeColumns()
    {
        $piece   = $this->consume();
        $columns = $this->columnsToArray($piece);

        $this->definition->setColumns($columns);
    }

    private function consumeForeignKey()
    {
        $piece = $this->consume();

        if (strtoupper($piece) === 'FOREIGN') {
            $this->consume(); // KEY

            $columns = [];
            $token   = $this->consume();

            while (null !== $token) {
                $columns = array_merge($columns, $this->columnsToArray($token));
                $token   = $this->consume();
                if (strtoupper($token) === 'REFERENCES') {
                    $this->putBack($token);

                    break;
                }
            }
            $this->definition->setColumns($columns);

            $this->consume(); // REFERENCES

            $referencedTable = $this->parseColumn($this->consume());
            $this->definition->setForeignTable($referencedTable);

            $referencedColumns = [];
            $token             = $this->consume();

            while (null !== $token) {
                $referencedColumns = array_merge($referencedColumns, $this->columnsToArray($token));
                $token             = $this->consume();
                if (strtoupper($token) === 'ON') {
                    $this->putBack($token);

                    break;
                }
            }

            $this->definition->setForeignColumns($referencedColumns);

            $this->consumeConstraintActions();
        } else {
            $this->putBack($piece);
        }
    }

    private function consumeConstraintActions()
    {
        while ($token = $this->consume()) {
            if (strtoupper($token) === 'ON') {
                $actionType   = strtolower($this->consume()); // UPDATE
                $actionMethod = strtolower($this->consume()); // CASCADE | NO ACTION | SET NULL | SET DEFAULT
                if ($actionMethod === 'no') {
                    $this->consume(); // consume ACTION
                    $actionMethod = 'restrict';
                } elseif ($actionMethod === 'set') {
                    $actionMethod = 'set ' . $this->consume(); // consume NULL or DEFAULT
                }
                $currentActions              = $this->definition->getConstraintActions();
                $currentActions[$actionType] = $actionMethod;
                $this->definition->setConstraintActions($currentActions);
            } else {
                $this->putBack($token);

                break;
            }
        }
    }
}
