<?php

namespace Leaf;

/**
 * Leaf MVC Core
 * ----------
 * Base class for configuring core methods
 */
class Core
{
    protected static $paths;

    /**
     * Return application paths
     * @return array
     */
    public static function paths(): array
    {
        return static::$paths;
    }

    /**
     * Setup MVC application based on config
     */
    public static function loadApplicationConfig()
    {
        static::loadConfig();

        if (class_exists('Leaf\Auth')) {
            auth()->config(Config::getStatic('mvc.config.auth'));
        }

        if (class_exists('Leaf\Mail')) {
            mailer()->connect(Config::getStatic('mvc.config.mail'));
        }

        if (php_sapi_name() !== 'cli') {
            app()->config(Config::getStatic('mvc.config.app'));

            if (class_exists('Leaf\Http\Cors')) {
                app()->cors(Config::getStatic('mvc.config.cors'));
            }

            if (class_exists('Leaf\Anchor\CSRF')) {
                $csrfConfig = Config::getStatic('mvc.config.csrf');

                $csrfEnabled = (
                    $csrfConfig &&
                    Config::getStatic('mvc.config.auth')['session'] ?? false
                );

                if ($csrfConfig['enabled'] ?? null !== null) {
                    $csrfEnabled = $csrfConfig['enabled'];
                }

                if ($csrfEnabled) {
                    app()->csrf($csrfConfig);
                }
            }

            if (class_exists('Leaf\Vite')) {
                \Leaf\Vite::config('assets', PublicPath('build'));
                \Leaf\Vite::config('build', 'public/build');
                \Leaf\Vite::config('hotFile', 'public/hot');
            }
        }
    }

    /**
     * Load all config files defined in the config folder
     */
    protected static function loadConfig()
    {
        static::$paths = PathsConfig();

        $config = [
            'app' => [
                'app.down' => _env('APP_DOWN', false),
                'debug' => _env('APP_DEBUG', true),
                'log.dir' => 'storage/logs/',
                'log.enabled' => true,
                'log.file' => 'app.log',
                'log.level' => Log::DEBUG,
                'log.open' => true,
                'log.writer' => null,
                'mode' => 'development',
                'views.path' => ViewsPath(null, false),
                'views.cachePath' => StoragePath('framework/views')
            ],
            'auth' => [
                'db.table' => 'users',
                'id.key' => 'id',
                'timestamps' => true,
                'timestamps.format' => 'YYYY-MM-DD HH:mm:ss',
                'unique' => ['email'],
                'hidden' => ['field.id', 'field.password'],
                'session' => true,
                'session.lifetime' => 60 * 60 * 24,
                'session.cookie' => ['secure' => false, 'httponly' => true, 'samesite' => 'lax'],
                'token.lifetime' => 60 * 60 * 24 * 365,
                'token.secret' => _env('TOKEN_SECRET', '@leaf$MVC*JWT#AUTH.Secret'),
                'messages.loginParamsError' => 'Incorrect credentials!',
                'messages.loginPasswordError' => 'Password is incorrect!',
                'password.key' => 'password',
                'password.encode' => function ($password) {
                    return \Leaf\Helpers\Password::hash($password);
                },
                'password.verify' => function ($password, $hashedPassword) {
                    return \Leaf\Helpers\Password::verify($password, $hashedPassword);
                },
            ],
            'cors' => [
                'origin' => '*',
                'methods' => 'GET,HEAD,PUT,PATCH,POST,DELETE',
                'allowedHeaders' => '*',
                'exposedHeaders' => '',
                'credentials' => false,
                'maxAge' => null,
                'preflightContinue' => false,
                'optionsSuccessStatus' => 204,
            ],
            'csrf' => [
                'secret' => _env('APP_KEY', '@nkor_leaf$0Secret!!'),
                'secretKey' => 'X-Leaf-CSRF-Token',
                'except' => [],
                'methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],
                'messages.tokenNotFound' => 'Token not found.',
                'messages.tokenInvalid' => 'Invalid token.',
                'onError' => null,
            ],
            'database' => [
                'default' => _env('DB_CONNECTION', 'mysql'),
                'connections' => [
                    'sqlite' => [
                        'driver' => 'sqlite',
                        'url' => _env('DATABASE_URL'),
                        'database' => _env('DB_DATABASE', AppPaths('databaseStorage') . '/database.sqlite'),
                        'prefix' => '',
                        'foreign_key_constraints' => _env('DB_FOREIGN_KEYS', true),
                    ],
                    'mysql' => [
                        'driver' => 'mysql',
                        'url' => _env('DATABASE_URL'),
                        'host' => _env('DB_HOST', '127.0.0.1'),
                        'port' => _env('DB_PORT', '3306'),
                        'database' => _env('DB_DATABASE', 'forge'),
                        'username' => _env('DB_USERNAME', 'forge'),
                        'password' => _env('DB_PASSWORD', ''),
                        'unix_socket' => _env('DB_SOCKET', ''),
                        'charset' => _env('DB_CHARSET', 'utf8mb4'),
                        'collation' => _env('DB_COLLATION', 'utf8mb4_unicode_ci'),
                        'prefix' => '',
                        'prefix_indexes' => true,
                        'strict' => true,
                        'engine' => null,
                        'options' => extension_loaded('pdo_mysql') ? array_filter([
                            \PDO::MYSQL_ATTR_SSL_CA => _env('MYSQL_ATTR_SSL_CA'),
                        ]) : [],
                    ],
                    'pgsql' => [
                        'driver' => 'pgsql',
                        'url' => _env('DATABASE_URL'),
                        'host' => _env('DB_HOST', '127.0.0.1'),
                        'port' => _env('DB_PORT', '5432'),
                        'database' => _env('DB_DATABASE', 'forge'),
                        'username' => _env('DB_USERNAME', 'forge'),
                        'password' => _env('DB_PASSWORD', ''),
                        'charset' => _env('DB_CHARSET', 'utf8'),
                        'prefix' => '',
                        'prefix_indexes' => true,
                        'schema' => 'public',
                        'sslmode' => 'prefer',
                    ],
                    'sqlsrv' => [
                        'driver' => 'sqlsrv',
                        'url' => _env('DATABASE_URL'),
                        'host' => _env('DB_HOST', 'localhost'),
                        'port' => _env('DB_PORT', '1433'),
                        'database' => _env('DB_DATABASE', 'forge'),
                        'username' => _env('DB_USERNAME', 'forge'),
                        'password' => _env('DB_PASSWORD', ''),
                        'charset' => _env('DB_CHARSET', 'utf8'),
                        'prefix' => '',
                        'prefix_indexes' => true,
                    ],
                ],
            ],
            'view' => [
                'viewEngine' => \Leaf\Blade::class,
                'render' => null,
                'config' => function ($config) {
                    \Leaf\Config::get('views.blade')->configure($config['views'], $config['cache']);
                },
            ],
            'mail' => [
                'host' => _env('MAIL_HOST', 'smtp.mailtrap.io'),
                'port' => _env('MAIL_PORT', 2525),
                'keepAlive' => true,
                'debug' => _env('MAIL_DEBUG', 'SERVER'),
                'security' => _env('MAIL_ENCRYPTION', 'STARTTLS'),
                'auth' => [
                    'username' => _env('MAIL_USERNAME'),
                    'password' => _env('MAIL_PASSWORD'),
                ],
                'defaults' => [
                    'senderName' => _env('MAIL_SENDER_NAME'),
                    'senderEmail' => _env('MAIL_SENDER_EMAIL'),
                    'replyToName' => _env('MAIL_REPLY_TO_NAME'),
                    'replyToEmail' => _env('MAIL_REPLY_TO_EMAIL'),
                ],
            ],
        ];

        foreach ($config as $configName => $config) {
            \Leaf\Config::set("mvc.config.$configName", $config);
        }

        $configPath = static::$paths['config'];
        $configFiles = glob("$configPath/*.php");

        foreach ($configFiles as $configFile) {
            $configName = basename($configFile, '.php');
            $config = require $configFile;

            \Leaf\Config::set("mvc.config.$configName", $config);
        }
    }

    /**
     * Load user defined libs
     */
    public static function loadLibs()
    {
        $libPath = static::$paths['lib'];
        $libFiles = glob("$libPath/*.php");

        foreach ($libFiles as $libFile) {
            require $libFile;
        }
    }

    /**
     * Load Aloe console and user defined commands
     */
    public static function loadConsole()
    {
        static::loadApplicationConfig();

        \Leaf\Database::connect();

        $console = new \Aloe\Console('v3.8.0');

        if (\Leaf\FS\Directory::exists(static::$paths['commands'])) {
            $consolePath = static::$paths['commands'];
            $consoleFiles = glob("$consolePath/*.php");

            foreach ($consoleFiles as $consoleFile) {
                $commandName = basename($consoleFile, '.php');

                $console->register(
                    "App\\Console\\$commandName",
                );
            }
        }

        $console->run();
    }

    /**
     * Load all application routes and run the application
     */
    public static function runApplication()
    {
        $routePath = static::$paths['routes'];
        $routeFiles = glob("$routePath/*.php");

        app()->setNamespace('\App\Controllers');

        require "$routePath/index.php";

        foreach ($routeFiles as $routeFile) {
            if (basename($routeFile) === 'index.php') {
                continue;
            }

            if (strpos(basename($routeFile), '_') !== 0) {
                continue;
            }

            require $routeFile;
        }

        app()->run();
    }
}
