<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Generators\ViewGeneratorInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ViewDefinition;

abstract class BaseViewGenerator implements ViewGeneratorInterface
{
    protected ViewDefinition $definition;

    public function __construct(string $viewName, ?string $schema = null)
    {
        $this->definition = new ViewDefinition([
            'driver' => static::driver(),
            'name'   => $viewName,
            'schema' => $schema,
        ]);
    }

    public function definition(): ViewDefinition
    {
        return $this->definition;
    }

    public static function init(string $viewName, ?string $schema = null): static
    {
        $instance = new static($viewName, $schema);

        if ($schema === null) {
            $instance->resolveSchema();
        }
        $instance->parse();

        return $instance;
    }
}
