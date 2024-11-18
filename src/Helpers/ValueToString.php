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

use BlitzPHP\Utilities\String\Text;

class ValueToString
{
    public static function castFloat($value)
    {
        return 'float$:' . $value;
    }

    public static function castBinary($value)
    {
        return 'binary$:' . $value;
    }

    public static function isCastedValue(?string $value): bool
    {
        if (null === $value) {
            return false;
        }

        return Text::startsWith($value, ['float$:', 'binary$:']);
    }

    public static function parseCastedValue(?string $value): ?string
    {
        if (null !== $value) {
            if (Text::startsWith($value, 'float$:')) {
                return str_replace('float$:', '', $value);
            }
            if (Text::startsWith($value, 'binary$:')) {
                return 'b\'' . str_replace('binary$:', '', $value) . '\'';
            }
        }

        return $value;
    }

    public static function make($value, $singleOutArray = false, $singleQuote = true)
    {
        $quote = $singleQuote ? '\'' : '"';
        if ($value === null) {
            return 'null';
        }
        if (is_array($value)) {
            if ($singleOutArray && count($value) === 1) {
                return $quote . $value[0] . $quote;
            }

            return '[' . collect($value)->map(fn ($item) => $quote . $item . $quote)->implode(', ') . ']';
        }
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (static::isCastedValue($value)) {
            return static::parseCastedValue($value);
        }

        if (Text::startsWith($value, $quote) && Text::endsWith($value, $quote)) {
            return $value;
        }

        return $quote . $value . $quote;
    }
}
