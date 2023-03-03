<?php

namespace Leaf;

use \Illuminate\Database\Capsule\Manager;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;

/**
 * Leaf Database Config
 * ---
 * This class is used to configure the database connection for models.
 */
class Database
{
    public static $capsule;

    protected static $config = [];

    /**
     * Set/Get database configuration
     * 
     * @param array $config The database configuration
     */
    public static function config($config = [])
    {
        if (empty($config)) {
            return static::$config;
        }

        static::$config = array_merge(static::$config, $config);
    }

    /**
     * Create a new database connection for models
     */
    public static function connect()
    {
        $connection = static::$config['default'] ?? 'mysql';

        static::$capsule = new Manager;
        static::$capsule->addConnection(
            static::$config['connections'][$connection]
        );

        static::$capsule->setEventDispatcher(new Dispatcher(new Container));
        static::$capsule->setAsGlobal();
        static::$capsule->bootEloquent();
    }

    /**
     * Create a Leaf Db connection using the model's database configuration
     */
    public static function syncLeafDb()
    {
        if (function_exists('db')) {
            db()->connect([
                'dbUrl' => static::$config['connections'][static::$config['default']]['url'] ?? null,
                'dbtype' => static::$config['default'] ?? 'mysql',
                'charset' => static::$config['connections'][static::$config['default']]['charset'] ?? 'utf8mb4',
                'port' => static::$config['connections'][static::$config['default']]['port'] ?? '3306',
                'host' => static::$config['connections'][static::$config['default']]['host'] ?? '127.0.0.1',
                'username' => static::$config['connections'][static::$config['default']]['username'] ?? 'root',
                'password' => static::$config['connections'][static::$config['default']]['password'] ?? '',
                'dbname' => static::$config['connections'][static::$config['default']]['database'] ?? 'leaf_db',
                'collation' => static::$config['connections'][static::$config['default']]['collation'] ?? 'utf8mb4_unicode_ci',
                'prefix' => static::$config['connections'][static::$config['default']]['prefix'] ?? '',
                'unix_socket' => static::$config['connections'][static::$config['default']]['unix_socket'] ?? '',
            ]);
        }
    }
}
