<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Definitions;

use BlitzPHP\Utilities\String\Text;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\StructureDefinitionInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ValueToString;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\WritableTrait;

class ColumnDefinition extends BaseDefinition implements StructureDefinitionInterface
{
    use WritableTrait;

    protected string $methodName;

    protected array $methodParameters = [];

    protected bool $unsigned = false;

    protected ?bool $nullable = null;

    protected $defaultValue;

    protected ?string $comment = null;

    protected ?string $characterSet = null;

    protected ?string $collation = null;

    protected bool $autoIncrementing = false;

    protected bool $index = false;

    protected bool $primary = false;

    protected bool $unique = false;

    protected bool $useCurrent = false;

    protected bool $useCurrentOnUpdate = false;

    protected ?string $storedAs = null;

    protected ?string $virtualAs = null;

    protected bool $isUUID = false;

    /** @var IndexDefinition[] */
    protected array $indexes = [];


    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getMethodParameters(): array
    {
        return $this->methodParameters;
    }

    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    public function getDefaultValue(): mixed
    {
        if (ValueToString::isCastedValue($this->defaultValue)) {
            return ValueToString::parseCastedValue($this->defaultValue);
        }

        return $this->defaultValue;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getCharacterSet(): ?string
    {
        return $this->characterSet;
    }

    public function getCollation(): ?string
    {
        return $this->collation;
    }

    public function isAutoIncrementing(): bool
    {
        return $this->autoIncrementing;
    }

    public function isIndex(): bool
    {
        return $this->index;
    }

    public function isPrimary(): bool
    {
        return $this->primary;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function useCurrent(): bool
    {
        return $this->useCurrent;
    }

    public function useCurrentOnUpdate(): bool
    {
        return $this->useCurrentOnUpdate;
    }

    public function getStoredAs(): ?string
    {
        return $this->storedAs;
    }

    public function getVirtualAs(): ?string
    {
        return $this->virtualAs;
    }

    public function isUUID(): bool
    {
        return $this->isUUID;
    }

    public function setMethodName(string $methodName): self
    {
        $this->methodName = $methodName;

        return $this;
    }

    public function setMethodParameters(array $methodParameters): self
    {
        $this->methodParameters = $methodParameters;

        return $this;
    }

    public function setUnsigned(bool $unsigned): self
    {
        $this->unsigned = $unsigned;

        return $this;
    }

    public function setNullable(?bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function setCharacterSet(?string $characterSet): self
    {
        $this->characterSet = $characterSet;

        return $this;
    }

    public function setCollation(?string $collation): self
    {
        $this->collation = $collation;

        return $this;
    }

    public function setAutoIncrementing(bool $autoIncrementing): self
    {
        $this->autoIncrementing = $autoIncrementing;

        return $this;
    }

    public function setStoredAs(?string $storedAs): self
    {
        $this->storedAs = $storedAs;

        return $this;
    }

    public function setVirtualAs(?string $virtualAs): self
    {
        $this->virtualAs = $virtualAs;

        return $this;
    }

    public function addIndex(IndexDefinition $definition): self
    {
        $this->indexes[] = $definition;

        return $this;
    }

    public function setIndex(bool $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function setPrimary(bool $primary): self
    {
        $this->primary = $primary;

        return $this;
    }

    public function setUnique(bool $unique): self
    {
        $this->unique = $unique;

        return $this;
    }

    public function setUseCurrent(bool $useCurrent): self
    {
        $this->useCurrent = $useCurrent;

        return $this;
    }

    public function setUseCurrentOnUpdate(bool $useCurrentOnUpdate): self
    {
        $this->useCurrentOnUpdate = $useCurrentOnUpdate;

        return $this;
    }

    public function setIsUUID(bool $isUUID): self
    {
        $this->isUUID = $isUUID;

        return $this;
    }


    protected function isNullableMethod(string $methodName): bool
    {
        return ! in_array($methodName, ['softDeletes', 'morphs', 'nullableMorphs', 'rememberToken', 'nullableUuidMorphs']) && ! $this->isPrimaryKeyMethod($methodName);
    }

    protected function isPrimaryKeyMethod(string $methodName): bool
    {
        return in_array($methodName, ['tinyIncrements', 'mediumIncrements', 'increments', 'bigIncrements', 'id']);
    }

    protected function canBeUnsigned(string $methodName): bool
    {
        return ! in_array($methodName, ['morphs', 'nullableMorphs']) && ! $this->isPrimaryKeyMethod($methodName);
    }

    protected function guessBlitzMethod()
    {
        if ($this->primary && $this->unsigned && $this->autoIncrementing) {
            //  une sorte de champ d'incrémentation
            if ($this->methodName === 'bigInteger') {
                if ($this->name === 'id') {
                    return [null, 'id', []];
                } else {
                    return [$this->name, 'bigIncrements', []];
                }
            } elseif ($this->methodName === 'mediumInteger') {
                return [$this->name, 'mediumIncrements', []];
            } elseif ($this->methodName === 'integer') {
                return [$this->name, 'increments', []];
            } elseif ($this->methodName === 'smallInteger') {
                return [$this->name, 'smallIncrements', []];
            } elseif ($this->methodName === 'tinyInteger') {
                return [$this->name, 'tinyIncrements', []];
            }
        }

        if ($this->methodName === 'tinyInteger' && ! $this->unsigned) {
            $boolean = false;
            if (in_array($this->defaultValue, ['true', 'false', true, false, 'TRUE', 'FALSE', '1', '0', 1, 0], true)) {
                $boolean = true;
            }
            if (Text::startsWith(strtoupper($this->name), ['IS_', 'HAS_'])) {
                $boolean = true;
            }
            if ($boolean) {
                return [$this->name, 'boolean', []];
            }
        }

        if ($this->methodName === 'morphs' && $this->nullable === true) {
            return [$this->name, 'nullableMorphs', []];
        }

        if ($this->methodName === 'uuidMorphs' && $this->nullable === true) {
            return [$this->name, 'nullableUuidMorphs', []];
        }

        if ($this->methodName === 'string' && $this->name === 'remember_token' && $this->nullable === true) {
            return [null, 'rememberToken', []];
        }
        if ($this->isUUID() && $this->methodName !== 'uuidMorphs') {
            // surcharge uniquement s'il n'y a pas déjà uuidMorphs
            return [$this->name, 'uuid', []];
        }

        if (ConfigResolver::get('definitions.prefer_unsigned_prefix') && $this->unsigned) {
            $availableUnsignedPrefixes = [
                'bigInteger',
                'decimal',
                'integer',
                'mediumInteger',
                'smallInteger',
                'tinyInteger'
            ];
            if (in_array($this->methodName, $availableUnsignedPrefixes)) {
                return [$this->name, 'unsigned' . ucfirst($this->methodName), $this->methodParameters];
            }
        }

        return [$this->name, $this->methodName, $this->methodParameters];
    }

    public function render(): string
    {
        [$finalname, $finalMethodName, $finalMethodParameters] = $this->guessBlitzMethod();

        $initialString = '$table->' . $finalMethodName . '(';
        if ($finalname !== null) {
            $initialString .= ValueToString::make($finalname);
        }
        if (count($finalMethodParameters) > 0) {
            foreach ($finalMethodParameters as $param) {
                $initialString .= ', ' . ValueToString::make($param);
            }
        }
        $initialString .= ')';
        if ($this->unsigned && $this->canBeUnsigned($finalMethodName) && ! Text::startsWith($finalMethodName, 'unsigned')) {
            $initialString .= '->unsigned()';
        }

        if ($this->defaultValue === 'NULL') {
            $this->defaultValue = null;
            $this->nullable = true;
        }

        if ($this->isNullableMethod($finalMethodName)) {
            if ($this->nullable === true) {
                $initialString .= '->nullable()';
            }
        }

        if ($this->defaultValue !== null) {
            $initialString .= '->default(';
            $initialString .= ValueToString::make($this->defaultValue, false);
            $initialString .= ')';
        }
        if ($this->useCurrent) {
            $initialString .= '->useCurrent()';
        }
        if ($this->useCurrentOnUpdate) {
            $initialString .= '->useCurrentOnUpdate()';
        }

        if ($this->index) {
            $indexName = '';
            if (count($this->indexes) === 1 && ConfigResolver::get('definitions.use_defined_index_names')) {
                $indexName = ValueToString::make($this->indexes[0]->getName());
            }
            $initialString .= '->index(' . $indexName . ')';
        }

        if ($this->primary && ! $this->isPrimaryKeyMethod($finalMethodName)) {
            $indexName = '';
            if (count($this->indexes) === 1 && ConfigResolver::get('definitions.use_defined_primary_key_index_names')) {
                if ($this->indexes[0]->getName() !== null) {
                    $indexName = ValueToString::make($this->indexes[0]->getName());
                }
            }
            $initialString .= '->primary(' . $indexName . ')';
        }

        if ($this->unique) {
            $indexName = '';
            if (count($this->indexes) === 1 && ConfigResolver::get('definitions.use_defined_unique_key_index_names')) {
                $indexName = ValueToString::make($this->indexes[0]->getName());
            }
            $initialString .= '->unique(' . $indexName . ')';
        }

        if ($this->storedAs !== null) {
            $initialString .= '->storedAs(' . ValueToString::make(str_replace('"', '\"', $this->storedAs), false, false) . ')';
        }

        if ($this->virtualAs !== null) {
            $initialString .= '->virtualAs(' . ValueToString::make(str_replace('"', '\"', $this->virtualAs), false, false) . ')';
        }

        if ($this->comment !== null && ConfigResolver::get('definitions.with_comments')) {
            $initialString .= '->comment(' . ValueToString::make(str_replace('"', '\"', $this->comment), false, false) . ')';
        }

        return $initialString;
    }
}
