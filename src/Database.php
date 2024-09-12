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

        $config = Config::getStatic('mvc.config.database');
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
            Schema::$capsule = static::$capsule;
        }
    }

    /**
     * Create a Leaf Db connection using the the default connection
     * defined in the config/database.php file
     *
     * @return \PDO|null
     */
    public static function initDb()
    {
        if (function_exists('db')) {
            $config = Config::getStatic('mvc.config.database');
            $defaultConnection = $config['connections'][$config['default'] ?? 'mysql'] ?? [];

            if (!empty($defaultConnection)) {
                return db()->connect([
                    'dbUrl' => $defaultConnection['url'] ?? null,
                    'dbtype' => $defaultConnection['driver'] ?? 'mysql',
                    'charset' => $defaultConnection['charset'] ?? 'utf8mb4',
                    'port' => $defaultConnection['port'] ?? '3306',
                    'host' => $defaultConnection['host'] ?? '127.0.0.1',
                    'username' => $defaultConnection['username'] ?? 'root',
                    'password' => $defaultConnection['password'] ?? '',
                    'dbname' => $defaultConnection['database'] ?? 'leaf_db',
                    'collation' => $defaultConnection['collation'] ?? 'utf8mb4_unicode_ci',
                    'prefix' => $defaultConnection['prefix'] ?? '',
                    'unix_socket' => $defaultConnection['unix_socket'] ?? '',
                ]);
            }
        }

        return null;
    }
}
