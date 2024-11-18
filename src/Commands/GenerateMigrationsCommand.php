<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Commands;

use Ahc\Cli\Output\Color;
use BlitzPHP\Cli\Console\Command;
use BlitzPHP\Database\Config\Services;
use BlitzPHP\Utilities\Iterable\Arr;
use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\GeneratorManagerInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\GeneratorManagers\MySQLGeneratorManager;
use Dimtrovich\BlitzPHP\MigrationGenerator\Helpers\ConfigResolver;
use Exception;

class GenerateMigrationsCommand extends Command
{
    /**
     * @var string Groupe
     */
    protected $group = 'BlitzPHP Migration Generator';

    /**
     * @var string Nom
     */
    protected $name = 'generate:migrations';

    /**
     * @var string Description
     */
    protected $description = 'Generate migrations from an existing database';

    /**
     * @var string
     */
    protected $service = 'Service de génération de code';

    /**
     * @var array Options
     */
    protected $options = [
        '--path'       => 'Le chemin où les migrations seront produites. Par défaut "default".',
        '--table'      => 'Générer des résultats uniquement pour les tables spécifiées. Par défaut "*".',
        '--view'       => 'Générer des résultats uniquement pour les vues spécifiées. Par défaut "*".',
        '--connection' => 'Utiliser une connexion de base de données différente de celle spécifiée dans la configuration de la base de données. Par défaut "auto".',
        '--empty-path' => 'Effacer les autres fichiers du dossier, par exemple si l\'on veut remplacer toutes les migrations.',
    ];

    public function getConnection(): string
    {
        $connection = $this->option('connection', 'auto');

        if ($connection === 'auto') {
            $connection = config('database.connection');
        }
        if ($connection === 'auto' && ! config()->has('database.' . $connection = environment())) {
            $connection = 'default';
        }

        if (! config()->has('database.' . $connection)) {
            throw new Exception('Impossible de trouver la connexion `' . $connection . '` dans votre configuration.');
        }

        return $connection;
    }

    public function getPath(string $driver): string
    {
        $basePath = $this->option('path', 'default');

        if ($basePath === 'default') {
            $basePath = ConfigResolver::path($driver);
        }

        return $basePath ?: '';
    }

    public function execute(array $params)
    {
        $timer = Services::timer()->start($this->name);

        try {
            $connection = $this->getConnection();
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return EXIT_ERROR;
        }

        $this->task('Utilisation de la connexion ' . $this->color->warn($connection), 1)->eol();

        Services::database($connection, false);
        $driver = str_ireplace('pdo', '', config('database.' . $connection . '.driver'));

        if (false === $manager = $this->resolveGeneratorManager($driver)) {
            $this->fail('Le pilote `' . $driver . '` n\'est pas pris en charge pour le moment.');

            return EXIT_ERROR;
        }

        $basePath = base_path($this->getPath($driver));

        if ($this->option('empty-path') || ConfigResolver::get('clear_output_path')) {
            foreach (glob($basePath . '/*.php') as $file) {
                unlink($file);
            }
        }

        $tableNames = Arr::wrap($this->option('table'));

        $viewNames = Arr::wrap($this->option('view'));

        ['tables' => $tables, 'views' => $views] = $manager->handle($basePath, $tableNames, $viewNames);

        if ([] !== $tables) {
            $this->justify('Tables', options: ['sep' => '-']);

            foreach ($tables as $name => $details) {
                $this->justify(
                    $this->color->ok($name, ['bold' => 1]) . ' (' . $details['time'] . ' ms)',
                    $this->color->warn(pathinfo($details['path'], PATHINFO_FILENAME))
                );
            }
        }

        if ([] !== $views) {
            $this->justify('Vues', options: ['sep' => '-']);

            foreach ($views as $name => $details) {
                $this->justify(
                    $this->color->ok($name, ['bold' => 1]) . ' (' . $details['time'] . ' ms)',
                    $this->color->warn(pathinfo($details['path'], PATHINFO_FILENAME))
                );
            }
        }

        $this->eol()->border(char: '*');

        $tableDuration = array_reduce($tables, fn ($acc, $details) => $details['time'] + $acc, 0);
        $viewDuration  = array_reduce($views, fn ($acc, $details) => $details['time'] + $acc, 0);

        $options = ['sep' => '-', 'second' => ['fg' => Color::GREEN]];
        $this->justify('Table générées', (string) count($tables), $options);
        $this->justify('Vues générées', (string) count($views), $options);
        $this->justify('Dossier de sortie', clean_path($basePath), $options);
        $this->justify('Connexion à la base de données', $connection, $options);
        $this->justify('Durée de génération des tables', $tableDuration . ' ms', $options);
        $this->justify('Durée de génération des vues', $viewDuration . ' ms', $options);
        $this->justify('Durée totale', ($tableDuration + $viewDuration + $timer->getElapsedTime($this->name) . ' ms'), $options);

        $this->border(char: '*');

        return EXIT_SUCCESS;
    }

    /**
     * @return false|GeneratorManagerInterface
     */
    protected function resolveGeneratorManager(string $driver)
    {
        $supported = [
            'mysql' => MySQLGeneratorManager::class,
        ];

        if (! isset($supported[$driver])) {
            return false;
        }

        return Services::factory($supported[$driver]);
    }
}
