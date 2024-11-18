<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Generators\MySQL;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Generators\ViewGeneratorInterface;
use Dimtrovich\BlitzPHP\MigrationGenerator\Generators\BaseViewGenerator;

class ViewGenerator extends BaseViewGenerator implements ViewGeneratorInterface
{
    public static function driver(): string
    {
        return 'mysql';
    }

    public function resolveSchema()
    {
        $structure = service('builder')->query('SHOW CREATE VIEW `' . $this->definition()->getName() . '`')->first();
        $structure = (array) $structure;
        if (isset($structure['Create View'])) {
            $this->definition()->setSchema($structure['Create View']);
        }
    }

    public function parse()
    {
        $schema = $this->definition()->getSchema();
        if (preg_match('/CREATE(.*?)VIEW/', $schema, $matches)) {
            $schema = str_replace($matches[1], ' ', $schema);
        }

        if (preg_match_all('/isnull\((.+?)\)/', $schema, $matches)) {
            foreach ($matches[0] as $key => $match) {
                $schema = str_replace($match, $matches[1][$key] . ' IS NULL', $schema);
            }
        }
        if (preg_match('/collate utf8mb4_unicode_ci/', $schema)) {
            $schema = str_replace('collate utf8mb4_unicode_ci', '', $schema);
        }
        $this->definition()->setSchema($schema);
    }
}
