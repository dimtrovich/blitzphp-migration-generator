<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\Concerns;

use BlitzPHP\Utilities\Date;
use BlitzPHP\Utilities\String\Text;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;

trait WritesViewsToFile
{
    use WritesToFile;

    public function stubNameVariables()
    {
        return [
            'ViewName:Studly'    => Text::studly($this->viewName),
            'ViewName:Lowercase' => strtolower($this->viewName),
            'ViewName'           => $this->viewName,
            'Timestamp'          => Date::now()->format(config('migrations.timestampFormat', 'Y-m-d-His_')),
        ];
    }

    protected function getStubFileName()
    {
        $driver = static::driver();

        $baseStubFileName = ConfigResolver::viewNamingScheme($driver);

        foreach ($this->stubNameVariables() as $variable => $replacement) {
            if (preg_match('/\\[' . $variable . '\\]/i', $baseStubFileName) === 1) {
                $baseStubFileName = preg_replace('/\\[' . $variable . '\\]/i', $replacement, $baseStubFileName);
            }
        }

        return $baseStubFileName;
    }

    protected function getStubPath(): string
    {
        return ConfigResolver::stub('view', static::driver());
    }

    protected function generateStub($tabCharacter = '    ')
    {
        $tab = str_repeat($tabCharacter, 3);

        $schema = $this->getSchema();
        $stub   = file_get_contents($this->getStubPath());
        $stub   = str_replace('[ViewName:Studly]', Text::studly($this->viewName), $stub);
        $stub   = str_replace('[ViewName]', $this->viewName, $stub);

        return str_replace('[Schema]', $tab . $schema, $stub);
    }
}
