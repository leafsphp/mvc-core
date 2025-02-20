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
     * Create a new database connection for models
     */
    public static function connect()
    {
        static::$capsule = new Manager;

        $config = Config::getStatic('mvc.config')['database'] ?? [];
        $connections = $config['connections'] ?? [];

        foreach ($connections as $name => $connection) {
            static::$capsule->addConnection(
                $connection,
                $config['default'] === $name ? 'default' : $name,
            );
        }

        static::$capsule->setEventDispatcher(new Dispatcher(new Container));
        static::$capsule->setAsGlobal();
        static::$capsule->bootEloquent();

        if (php_sapi_name() === 'cli') {
            Schema::setDbConnection(static::$capsule);
        }
    }

    /**
     * Create a Leaf Db connection using the the default connection
     * defined in the config/database.php file
     */
    public static function initDb()
    {
        if (function_exists('db')) {
            $connections = [];
            $config = Config::getStatic('mvc.config')['database'] ?? [];

            foreach ($config['connections'] as $key => $connection) {
                $connections[$key] = [
                    'dbUrl' => $connection['url'] ?? null,
                    'dbtype' => $connection['driver'],
                    'charset' => $connection['charset'] ?? null,
                    'port' => $connection['port'] ?? null,
                    'host' => $connection['host'] ?? null,
                    'username' => $connection['username'] ?? null,
                    'password' => $connection['password'] ?? null,
                    'dbname' => $connection['database'],
                    'collation' => $connection['collation'] ?? 'utf8mb4_unicode_ci',
                    'prefix' => $connection['prefix'] ?? '',
                    'unix_socket' => $connection['unix_socket'] ?? '',
                ];
            }

            db()->addConnections($connections, $config['default']);
        }

        return null;
    }
}
