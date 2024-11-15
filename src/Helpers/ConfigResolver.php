<?php

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Helpers;

use BlitzPHP\Utilities\Iterable\Arr;

class ConfigResolver
{
    private static $config = [];

    public static function get(string $key)
    {
        if (null !== $config = config('blitzphp-migration-generator.' . $key)) {
            return $config;
        }

        return self::retrieve($key);
    }

    public static function stub(string $key, string $driver): string
    {
        $path =  __DIR__ . '/../../stubs/' . $key . '.stub';

        if (! function_exists('resource_path')) {
            return $path;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/blitzphp-migration-generator/' . $driver . '-' . $key . '.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/blitzphp-migration-generator/'. $key  .'.stub'))) {
            return $overridden;
        }

        return $path;
    }


    protected static function retrieve(string $key): mixed 
    {
        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../Config/blitzphp-migration-generator.php';
        }

        return Arr::getRecursive(self::$config, $key);
    }

    protected static function resolver(string $configKey, string $driver)
    {
        return ($override = self::get($driver . '.' . $configKey)) !== null ?
            $override : self::get($configKey);
    }

    public static function tableNamingScheme(string $driver)
    {
        return static::resolver('table_naming_scheme', $driver);
    }

    public static function viewNamingScheme(string $driver)
    {
        return static::resolver('view_naming_scheme', $driver);
    }

    public static function path(string $driver)
    {
        return static::resolver('path', $driver);
    }

    public static function skippableTables(string $driver)
    {
        return array_map('trim', explode(',', static::resolver('skippable_tables', $driver)));
    }

    public static function skippableViews(string $driver)
    {
        return array_map('trim', explode(',', static::resolver('skippable_views', $driver)));
    }
}
