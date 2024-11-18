<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Tokenizers;

use Dimtrovich\BlitzPHP\MigrationGenerator\Contracts\Tokenizers\TokenizerInterface;

abstract class BaseTokenizer implements TokenizerInterface
{
    private const SPACE_REPLACER        = '&!@';
    private const SINGLE_QUOTE_REPLACER = '!*@';

    protected $tokens = [];

    public function __construct(private string $value)
    {
        $prune             = false;
        $pruneSingleQuotes = false;

        // first get rid of any single quoted stuff with '' around it
        if (preg_match_all('/\'\'(.+?)\'\'/', $value, $matches)) {
            foreach ($matches[0] as $key => $singleQuoted) {
                $toReplace         = $singleQuoted;
                $value             = str_replace($toReplace, self::SINGLE_QUOTE_REPLACER . $matches[1][$key] . self::SINGLE_QUOTE_REPLACER, $value);
                $pruneSingleQuotes = true;
            }
        }
        if (preg_match('/\'\'/', $value)) {
            $value = str_replace('\'\'', '$$EMPTY_STRING', $value);
        }

        if (preg_match_all("/'(.+?)'/", $value, $matches)) {
            foreach ($matches[0] as $quoteWithSpace) {
                // we've got an enum or set that has spaces in the text
                // so we'll convert to a different character so it doesn't get pruned
                $toReplace = $quoteWithSpace;
                $value     = str_replace($toReplace, str_replace(' ', self::SPACE_REPLACER, $toReplace), $value);
                $prune     = true;
            }
        }
        $value        = str_replace('$$EMPTY_STRING', '\'\'', $value);
        $this->tokens = array_map(fn ($item) => trim($item, ', '), str_getcsv($value, ' ', "'"));

        if ($prune) {
            $this->tokens = array_map(fn ($item) => str_replace(self::SPACE_REPLACER, ' ', $item), $this->tokens);
        }
        if ($pruneSingleQuotes) {
            $this->tokens = array_map(fn ($item) => str_replace(self::SINGLE_QUOTE_REPLACER, '\'', $item), $this->tokens);
        }
    }

    public static function make(string $line)
    {
        return new static($line);
    }

    public static function parse(string $line): static
    {
        return (new static($line))->tokenize();
    }

    protected function parseColumn($value)
    {
        return trim($value, '` ');
    }

    protected function columnsToArray($string)
    {
        $string = trim($string, '()');

        return array_map(fn ($item) => $this->parseColumn($item), explode(',', $string));
    }

    protected function consume()
    {
        return array_shift($this->tokens) ?? '';
    }

    protected function putBack($value)
    {
        array_unshift($this->tokens, $value);
    }
}
