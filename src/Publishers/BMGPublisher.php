<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Publishers;

use BlitzPHP\Publisher\Publisher;

class BMGPublisher extends Publisher
{
    /**
     * {@inheritDoc}
     */
    protected string $source = __DIR__ . '/../Config/';

    /**
     * {@inheritDoc}
     */
    protected string $destination = CONFIG_PATH;

    /**
     * {@inheritDoc}
     */
    public function publish(): bool
    {
        return $this->addPaths(['blitzphp-migration-generator.php'])->merge(false);
    }
}
