<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Generators\TableGeneratorInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\TableDefinition;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns\CleansUpMorphColumns;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns\CleansUpColumnIndices;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns\CleansUpTimestampsColumn;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns\CleansUpForeignKeyIndices;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;

abstract class BaseTableGenerator implements TableGeneratorInterface
{
    use CleansUpForeignKeyIndices;
    use CleansUpMorphColumns;
    use CleansUpTimestampsColumn;
    use CleansUpColumnIndices;

    protected array $rows = [];

    protected TableDefinition $definition;

    public function __construct(string $tableName, array $rows = [])
    {
        $this->definition = new TableDefinition([
            'driver' => static::driver(),
            'name'   => $tableName
        ]);

        $this->rows = $rows;
    }

    public function definition(): TableDefinition
    {
        return $this->definition;
    }

    public static function init(string $tableName, array $rows = []): static
    {
        $instance = (new static($tableName, $rows));

        if ($instance->shouldResolveStructure()) {
            $instance->resolveStructure();
        }

        $instance->parse();
        $instance->cleanUp();

        return $instance;
    }

    public function shouldResolveStructure(): bool
    {
        return count($this->rows) === 0;
    }

    public function cleanUp(): void
    {
        $this->cleanUpForeignKeyIndices();

        $this->cleanUpMorphColumns();

        if (! ConfigResolver::get('definitions.use_defined_datatype_on_timestamp')) {
            $this->cleanUpTimestampsColumn();
        }

        $this->cleanUpColumnsWithIndices();
    }
}
