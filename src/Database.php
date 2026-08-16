<?php

namespace Leaf;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

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
        static::$capsule = new Manager();

        $config = Config::getStatic('mvc.config')['database'] ?? [];
        $connections = $config['connections'] ?? [];

        foreach ($connections as $name => $connection) {
            if (($connection['driver'] ?? null) === 'sqlite') {
                $connection['journal_mode'] = $connection['journal_mode'] ?? _env('DB_JOURNAL_MODE', 'wal');
                $connection['busy_timeout'] = $connection['busy_timeout'] ?? (int) _env('DB_BUSY_TIMEOUT', 5000);
            }

            static::$capsule->addConnection(
                $connection,
                $config['default'] === $name ? 'default' : $name,
            );
        }

        static::$capsule->setEventDispatcher(new Dispatcher(new Container()));
        static::$capsule->setAsGlobal();
        static::$capsule->bootEloquent();

        if (function_exists('crash')) {
            foreach (array_keys($connections) as $name) {
                static::$capsule
                    ->getConnection($config['default'] === $name ? 'default' : $name)
                    ->listen(function ($query) {
                        crash()->leaveCrumb($query->sql, 'query', [
                            'ms' => $query->time,
                        ], false);
                    });
            }
        }

        if (php_sapi_name() === 'cli' && class_exists('Leaf\Schema')) {
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

            if (method_exists(db(), 'connectionResolver')) {
                foreach (array_keys($config['connections']) as $name) {
                    $connectionName = $config['default'] === $name ? 'default' : $name;

                    db()->connectionResolver(function () use ($connectionName) {
                        if (!static::$capsule) {
                            static::connect();
                        }

                        return static::$capsule->getConnection($connectionName)->getPdo();
                    }, $connectionName);
                }
            }
        }

        return null;
    }
}
