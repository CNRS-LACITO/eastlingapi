<?php

use Illuminate\Support\Str;


    return array(

        /*
        |--------------------------------------------------------------------------
        | PDO Fetch Style
        |--------------------------------------------------------------------------
        |
        | By default, database results will be returned as instances of the PHP
        | stdClass object; however, you may desire to retrieve records in an
        | array format for simplicity. Here you can tweak the fetch style.
        |
        */
        'fetch' => PDO::FETCH_CLASS,

        /*
        |--------------------------------------------------------------------------
        | Default Database Connection Name
        |--------------------------------------------------------------------------
        |
        | Here you may specify which of the database connections below you wish
        | to use as your default connection for all database work. Of course
        | you may use many connections at once using the Database library.
        |
        */
        'default' => env('DB_CONNECTION', 'mysql'),

        /*
        |--------------------------------------------------------------------------
        | Database Connections
        |--------------------------------------------------------------------------
        |
        | Here are each of the database connections setup for your application.
        | Of course, examples of configuring each database platform that is
        | supported by Laravel is shown below to make development simple.
        |
        |
        | All database work in Laravel is done through the PHP PDO facilities
        | so make sure you have the driver for your particular database of
        | choice installed on your machine before you begin development.
        |
        */

        'connections' => array(

            'mysql' => array('driver' => 'mysql',
                            'connection' => env('DB_CONNECTION', 'mysql'),
                             'host'       => env('DB_HOST', '127.0.0.1'),
                             'port'       => env('DB_PORT', '3306'),
                             'database'   => env('DB_DATABASE', 'eastling'),
                             'username'   => env('DB_USERNAME', 'eastling'),
                             'password'   => env('DB_PASSWORD', 'vacCe.QuegpiOwm0'),
                             'charset'    => 'utf8',
                             'collation'  => 'utf8_general_ci',
                             'prefix'     => '',
                             'sslmode' => env('DB_SSLMODE', 'prefer'),
                             'options'    => env('APP_ENV')==='local' ? null : array(
                                                   PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => 'false',
                                                   PDO::MYSQL_ATTR_SSL_CA =>env('DB_MYSQL_ATTR_SSL_CA','/shared/hncert/__db_huma-num_fr_interm_root.cer')
                                               ),

                        ),

        ),
        
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
        'migrations' => 'migrations',

        /*
        |--------------------------------------------------------------------------
        | Redis Databases
        |--------------------------------------------------------------------------
        |
        | Redis is an open source, fast, and advanced key-value store that also
        | provides a richer set of commands than a typical key-value systems
        | such as APC or Memcached. Laravel makes it easy to dig right in.
        |
        */
        'redis' => array(
            'cluster' => false,
            'default' => array(
                'host'     => '127.0.0.1',
                'port'     => 6379,
                'database' => 0,
            ),
        ),

    ); 

?>

