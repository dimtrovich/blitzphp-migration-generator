<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Formatters;

use BlitzPHP\Utilities\Date;
use BlitzPHP\Utilities\String\Text;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\FormatterInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\Formatter;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;
use Dimtrovich\BlitzPHP\MigrationGenerator\Definitions\ViewDefinition;

class ViewFormatter implements FormatterInterface
{
    public function __construct(private ViewDefinition $view)
    {
    }

    public function stubNameVariables(int $index = 0): array
    {
        return [
            'ViewName:Studly'       => Text::studly($viewName = $this->view->getName()),
            'ViewName:Lowercase'    => strtolower($viewName),
            'ViewName'              => $viewName,
            'Timestamp'             => Date::now()->format($timestampFormat = config('migrations.timestampFormat', 'Y-m-d-His_')),
            'Index'                 => '0000-00-00_' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            'IndexedEmptyTimestamp' => '0000-00-00_' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            'IndexedTimestamp'      => Date::now()->addSeconds($index)->format($timestampFormat)
        ];
    }

    protected function getStubFileName(int $index = 0)
    {
        $driver = $this->view->getDriver();

        $baseStubFileName = ConfigResolver::viewNamingScheme($driver);
        foreach ($this->stubNameVariables($index) as $variable => $replacement) {
            if (preg_match("/\[" . $variable . "\]/i", $baseStubFileName) === 1) {
                $baseStubFileName = preg_replace("/\[" . $variable . "\]/i", $replacement, $baseStubFileName);
            }
        }

        return $baseStubFileName;
    }

    protected function getStubPath()
    {
        return ConfigResolver::stub('view', $this->view->getDriver());
    }

	/**
	 * {@inheritDoc}
	 */
    public function render(string $tabCharacter = '    '): string
    {
        $schema = $this->view->getSchema();
        $stub = file_get_contents($this->getStubPath());
        $variables = [
            '[ViewName:Studly]' => Text::studly($viewName = $this->view->getName()),
            '[ViewName]'        => $viewName,
            '[Schema]'          => $schema
        ];
        foreach ($variables as $key => $value) {
            $stub = Formatter::replace($tabCharacter, $key, $value, $stub);
        }

        return $stub;
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
