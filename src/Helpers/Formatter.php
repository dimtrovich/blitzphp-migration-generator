<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Helpers;

use BlitzPHP\Utilities\Helpers;

class Formatter
{
    private $lines        = [];
    private bool $isSpace = true;

    public function __construct(private string $tabCharacter = '    ')
    {
        $this->isSpace = ! str_contains($tabCharacter, "\t");
    }

    public function line(string $data, $indentTimes = 0)
    {
        $this->lines[] = str_repeat($this->tabCharacter, $indentTimes) . $data;

        return fn ($data) => $this->line($data, $indentTimes + 1);
    }

    public function render($extraIndent = 0)
    {
        $lines = $this->lines;
        if ($extraIndent > 0) {
            $lines = Helpers::collect($lines)->map(function ($item, $index) use ($extraIndent) {
                if ($index === 0) {
                    return $item;
                }

                return str_repeat($this->tabCharacter, $extraIndent) . $item;
            })->toArray();
        }

        return implode("\n", $lines);
    }

    public function replaceOnLine($toReplace, $body)
    {
        if (preg_match('/^(\s+)?' . preg_quote($toReplace) . '/m', $body, $matches) !== false) {
            $gap       = $matches[1] ?? '';
            $numSpaces = strlen($this->tabCharacter);
            if ($numSpaces === 0) {
                $startingTabIndent = 0;
            } else {
                $startingTabIndent = (int) (strlen($gap) / $numSpaces);
            }

            return preg_replace('/' . preg_quote($toReplace) . '/', $this->render($startingTabIndent), $body);
        }

        return $body;
    }

    public static function replace($tabCharacter, $toReplace, $replacement, $body)
    {
        $formatter = new static($tabCharacter);

        foreach (explode("\n", $replacement) as $line) {
            $formatter->line($line);
        }

        return $formatter->replaceOnLine($toReplace, $body);
    }
}
