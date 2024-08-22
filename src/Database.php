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
    /**@var \Illuminate\Database\Capsule\Manager $capsule */
    public static $capsule;

    /**
     * @deprecated use Leaf\Config::get('_database') instead
     */
    protected static $config = [];

    /**
     * Set/Get database configuration
     * @param array $config The database configuration
     * 
     * @deprecated Config can be done through Leaf\Config
     */
    public static function config($config = [])
    {
        $initialConfig = Config::getStatic('_database');

        if (empty($config)) {
            return $initialConfig;
        }

        Config::set('_database', array_merge($initialConfig, $config));
    }

    /**
     * Create a new database connection for models
     */
    public static function connect()
    {
        $config = Config::get('_database');
        $connection = $config['default'] ?? 'mysql';

        static::$capsule = new Manager;
        static::$capsule->addConnection(
            $config['connections'][$connection]
        );

        static::$capsule->setEventDispatcher(new Dispatcher(new Container));
        static::$capsule->setAsGlobal();
        static::$capsule->bootEloquent();

        if (php_sapi_name() === 'cli') {
            Schema::$capsule = static::$capsule;
        }
    }

    /**
     * Create a Leaf Db connection using the model's database configuration
     */
    public static function initDb()
    {
        if (function_exists('db')) {
            $config = Config::get('_database');

            db()->connect([
                'dbUrl' => $config['connections'][$config['default']]['url'] ?? null,
                'dbtype' => $config['default'] ?? 'mysql',
                'charset' => $config['connections'][$config['default']]['charset'] ?? 'utf8mb4',
                'port' => $config['connections'][$config['default']]['port'] ?? '3306',
                'host' => $config['connections'][$config['default']]['host'] ?? '127.0.0.1',
                'username' => $config['connections'][$config['default']]['username'] ?? 'root',
                'password' => $config['connections'][$config['default']]['password'] ?? '',
                'dbname' => $config['connections'][$config['default']]['database'] ?? 'leaf_db',
                'collation' => $config['connections'][$config['default']]['collation'] ?? 'utf8mb4_unicode_ci',
                'prefix' => $config['connections'][$config['default']]['prefix'] ?? '',
                'unix_socket' => $config['connections'][$config['default']]['unix_socket'] ?? '',
            ]);
        }
    }
}
