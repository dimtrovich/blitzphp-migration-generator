<?php

return [
    'clear_output_path'    => env('BMG_CLEAR_OUTPUT_PATH', false),

	// Configuration par défaut
    'table_naming_scheme' => env('BMG_TABLE_NAMING_SCHEME', '[IndexedTimestamp]Create[TableName:Studly]Table.php'),
    'view_naming_scheme'  => env('BMG_VIEW_NAMING_SCHEME', '[IndexedTimestamp]Create[ViewName:Studly]View.php'),
    'path'                => env('BMG_OUTPUT_PATH', 'app/Database/Migrations'),
    'skippable_tables'    => env('BMG_SKIPPABLE_TABLES', 'migrations'),
    'skip_views'          => env('BMG_SKIP_VIEWS', false),
    'skippable_views'     => env('BMG_SKIPPABLE_VIEWS', ''),
    'sort_mode'           => env('BMG_SORT_MODE', 'foreign_key'),
    'definitions'         => [
        'prefer_unsigned_prefix'              => env('BMG_PREFER_UNSIGNED_PREFIX', true),
        'use_defined_index_names'             => env('BMG_USE_DEFINED_INDEX_NAMES', true),
        'use_defined_foreign_key_index_names' => env('BMG_USE_DEFINED_FOREIGN_KEY_INDEX_NAMES', true),
        'use_defined_unique_key_index_names'  => env('BMG_USE_DEFINED_UNIQUE_KEY_INDEX_NAMES', true),
        'use_defined_primary_key_index_names' => env('BMG_USE_DEFINED_PRIMARY_KEY_INDEX_NAMES', true),
        'with_comments'                       => env('BMG_WITH_COMMENTS', true),
        'use_defined_datatype_on_timestamp'   => env('BMG_USE_DEFINED_DATATYPE_ON_TIMESTAMP', false),
    ],

    // Configuration spécifique aux pilotes de base de données
    //null = use default
    'mysql' => [
        'table_naming_scheme' => env('BMG_MYSQL_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('BMG_MYSQL_VIEW_NAMING_SCHEME', null),
        'path'                => env('BMG_MYSQL_OUTPUT_PATH', null),
        'skippable_tables'    => env('BMG_MYSQL_SKIPPABLE_TABLES', null),
        'skippable_views'     => env('BMG_MYSQL_SKIPPABLE_VIEWS', null),
    ],
    'sqlite' => [
        'table_naming_scheme' => env('BMG_SQLITE_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('BMG_SQLITE_VIEW_NAMING_SCHEME', null),
        'path'                => env('BMG_SQLITE_OUTPUT_PATH', null),
        'skippable_tables'    => env('BMG_SQLITE_SKIPPABLE_TABLES', null),
        'skippable_views'     => env('BMG_SQLITE_SKIPPABLE_VIEWS', null),
    ],
    'pgsql' => [
        'table_naming_scheme' => env('BMG_PGSQL_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('BMG_PGSQL_VIEW_NAMING_SCHEME', null),
        'path'                => env('BMG_PGSQL_OUTPUT_PATH', null),
        'skippable_tables'    => env('BMG_PGSQL_SKIPPABLE_TABLES', null),
        'skippable_views'     => env('BMG_PGSQL_SKIPPABLE_VIEWS', null)
    ],
    'sqlsrv' => [
        'table_naming_scheme' => env('BMG_SQLSRV_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('BMG_SQLSRV_VIEW_NAMING_SCHEME', null),
        'path'                => env('BMG_SQLSRV_OUTPUT_PATH', null),
        'skippable_tables'    => env('BMG_SQLSRV_SKIPPABLE_TABLES', null),
        'skippable_views'     => env('BMG_SQLSRV_SKIPPABLE_VIEWS', null),
    ],
];
