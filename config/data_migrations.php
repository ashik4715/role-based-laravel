<?php

/*
|--------------------------------------------------------------------------
| Data Migration Configurations
|--------------------------------------------------------------------------
*/
return [
    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */
    'table_name' => 'data_migrations',

    /*
    |--------------------------------------------------------------------------
    | Migration Default namespace
    |--------------------------------------------------------------------------
    |
    | This determines where the migrations classes will be stored.
    |
    */
    'namespace' => 'Database\\DataMigrations',

    /*
    |--------------------------------------------------------------------------
    | Migration Default Directory
    |--------------------------------------------------------------------------
    |
    | This determines where the migrations classes will be stored.
    |
    */
    'directory' => 'database/data-migrations',
];
