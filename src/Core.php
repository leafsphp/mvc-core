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

    protected static $mode = 'web';

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

        \Leaf\Database::initDb();

        if (php_sapi_name() !== 'cli') {
            if (class_exists('Leaf\Vite')) {
                \Leaf\Vite::config('assets', PublicPath('build'));
                \Leaf\Vite::config('build', 'public/build');
                \Leaf\Vite::config('hotFile', 'public/hot');
            }

            if (storage()->exists(LibPath())) {
                static::loadLibs();
            }

            if (storage()->exists('app/index.php')) {
                require 'app/index.php';
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
                'log.enabled' => _env('APP_LOG_ENABLED', true),
                'log.file' => 'app.log',
                'log.level' => Log::DEBUG,
                'log.open' => true,
                'log.writer' => null,
                'mode' => _env('APP_ENV', 'development'),
                'views.path' => ViewsPath(null, false),
                'views.cachePath' => StoragePath('framework/views')
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
                'config' => function ($engine, $viewConfig) {
                    $engine->configure($viewConfig['views'], $viewConfig['cache']);
                },
                'render' => null,
                'extend' => null,
            ],
        ];

        if (storage()->exists($configPath = static::$paths['config'])) {
            foreach (glob("$configPath/*.php") as $configFile) {
                $config[basename($configFile, '.php')] = require $configFile;
            }
        }

        app()->config($config['app']);

        if ($config['view']['viewEngine']) {
            Config::attachView($config['view']['viewEngine'], 'template');

            if ($config['view']['config']) {
                call_user_func_array($config['view']['config'], [
                    app()->template(),
                    [
                        'views' => $config['app']['views.path'],
                        'cache' => $config['app']['views.cachePath'],
                    ]
                ]);
            } else if (method_exists(app()->template(), 'configure')) {
                app()->template()->configure([
                    'views' => $config['app']['views.path'],
                    'cache' => $config['app']['views.cachePath'],
                ]);
            }

            if (is_callable($config['view']['extend'])) {
                call_user_func($config['view']['extend'], app()->template());
            }
        }

        if (class_exists('Leaf\Auth')) {
            $config['auth'] = array_merge([
                'db.table' => _env('AUTH_DB_TABLE', 'users'),
                'id.key' => _env('AUTH_DB_ID', 'id'),
                'timestamps' => _env('AUTH_TIMESTAMPS', true),
                'timestamps.format' => _env('AUTH_TIMESTAMPS_FORMAT', 'YYYY-MM-DD HH:mm:ss'),
                'unique' => ['email'],
                'hidden' => ['field.id', 'field.password'],
                'session' => _env('AUTH_SESSION', true),
                'session.lifetime' => 60 * 60 * 24,
                'session.cookie' => ['secure' => false, 'httponly' => true, 'samesite' => 'lax'],
                'token.lifetime' => 60 * 60 * 24 * 365,
                'token.secret' => _env('AUTH_TOKEN_SECRET', '@leaf$MVC*JWT#AUTH.Secret'),
                'messages.loginParamsError' => 'Incorrect credentials!',
                'messages.loginPasswordError' => 'Password is incorrect!',
                'password.key' => 'password',
                'password.encode' => function ($password) {
                    return \Leaf\Helpers\Password::hash($password);
                },
                'password.verify' => function ($password, $hashedPassword) {
                    return \Leaf\Helpers\Password::verify($password, $hashedPassword);
                },
            ], $config['auth'] ?? []);

            auth()->config($config['auth']);
        }

        if (class_exists('Leaf\Http\Cors')) {
            $config['cors'] = array_merge([
                'origin' => _env('CORS_ALLOWED_ORIGINS', '*'),
                'methods' => _env('CORS_ALLOWED_METHODS', 'GET,HEAD,PUT,PATCH,POST,DELETE'),
                'allowedHeaders' => _env('CORS_ALLOWED_HEADERS', '*'),
                'exposedHeaders' => _env('CORS_EXPOSED_HEADERS', ''),
                'credentials' => false,
                'maxAge' => null,
                'preflightContinue' => false,
                'optionsSuccessStatus' => 204,
            ], $config['cors'] ?? []);

            if (php_sapi_name() !== 'cli') {
                app()->cors($config['cors']);
            }
        }

        if (class_exists('Leaf\Anchor\CSRF')) {
            $config['csrf'] = array_merge([
                'secret' => _env('APP_KEY', '@nkor_leaf$0Secret!!'),
                'secretKey' => 'X-Leaf-CSRF-Token',
                'except' => [],
                'methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],
                'messages.tokenNotFound' => 'Token not found.',
                'messages.tokenInvalid' => 'Invalid token.',
                'onError' => null,
            ], $config['csrf'] ?? []);

            $csrfEnabled = (
                $config['csrf'] &&
                $config['auth']['session'] ?? false
            );

            if (($config['csrf']['enabled'] ?? null) !== null) {
                $csrfEnabled = $config['csrf']['enabled'];
            }

            if ($csrfEnabled && php_sapi_name() !== 'cli') {
                app()->csrf($config['csrf']);
            }
        }

        if (class_exists('Leaf\Mail')) {
            $config['mail'] = array_merge([
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
            ], $config['mail'] ?? []);

            mailer()->connect($config['mail']);
        }

        if (class_exists('Leaf\Queue')) {
            $config['queue'] = array_merge([
                'default' => _env('QUEUE_CONNECTION', 'database'),
                'connections' => [
                    'redis' => [
                        'driver' => 'redis',
                        'connection' => _env('REDIS_QUEUE_CONNECTION', 'default'),
                        'table' => _env('REDIS_QUEUE', 'leaf_php_jobs'),
                    ],
                    'database' => [
                        'driver' => 'database',
                        'connection' => _env('DB_QUEUE_CONNECTION', 'default'),
                        'table' => _env('DB_QUEUE_TABLE', 'leaf_php_jobs'),
                    ],
                ],
            ], $config['queue'] ?? []);
        }

        if (class_exists('Leaf\Redis')) {
            $config['redis'] = array_merge([
                'port' => _env('REDIS_PORT', 6379),
                'scheme' => 'tcp',
                'password' => _env('REDIS_PASSWORD', null),
                'host' => _env('REDIS_HOST', '127.0.0.1'),
                'session' => false,
                'session.savePath' => null,
                'session.saveOptions' => [],
                'connection.timeout' => 0.0,
                'connection.reserved' => null,
                'connection.retryInterval' => 0,
                'connection.readTimeout' => 0.0,
            ], $config['redis'] ?? []);

            redis()->connect($config['redis']);
        }

        if (
            class_exists('Leaf\Billing\Stripe') ||
            class_exists('Leaf\Billing\PayStack') ||
            class_exists('Leaf\Billing\LemonSqueezy')
        ) {
            $config['billing'] = array_merge([
                'default' => _env('BILLING_PROVIDER', 'stripe'),
                'connections' => [
                    'stripe' => [
                        'driver' => 'stripe',
                        'secrets.apiKey' => _env('STRIPE_API_KEY'),
                        'secrets.publishableKey' => _env('STRIPE_PUBLISHABLE_KEY'),
                        'secrets.webhook' => _env('STRIPE_WEBHOOK_SECRET'),
                        'version' => _env('STRIPE_API_VERSION', '2023-10-16'),
                        'currency' => [
                            'name' => _env('STRIPE_CURRENCY', 'usd'),
                            'symbol' => _env('STRIPE_CURRENCY_SYMBOL', '$'),
                            'display' => _env('STRIPE_CURRENCY_DISPLAY', 'USD'),
                            'locale' => _env('STRIPE_CURRENCY_LOCALE', 'en_US'),
                        ],
                    ],
                    'paystack' => [
                        'driver' => 'paystack',
                        'secrets.apiKey' => _env('PAYSTACK_API_KEY'),
                        'secrets.publishableKey' => _env('PAYSTACK_PUBLISHABLE_KEY'),
                        'secrets.webhook' => _env('PAYSTACK_WEBHOOK_SECRET'),
                        'version' => _env('PAYSTACK_API_VERSION', null),
                        'currency' => [
                            'name' => _env('PAYSTACK_CURRENCY', 'ngn'),
                            'symbol' => _env('PAYSTACK_CURRENCY_SYMBOL', '₦'),
                            'display' => _env('PAYSTACK_CURRENCY_DISPLAY', 'NGN'),
                            'locale' => _env('PAYSTACK_CURRENCY_LOCALE', 'en_US'),
                        ],
                    ],
                ],
                'urls' => [
                    'success' => _env('BILLING_SUCCESS_URL', '/billing/callback'),
                    'cancel' => _env('BILLING_CANCEL_URL', '/billing/callback'),
                ],
                'tiers' => []
            ], $config['billing'] ?? []);

            billing($config['billing']);
        }

        Config::set('mvc.config', $config);
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
     * Set mode for Leaf MVC: API or Web
     */
    public static function mode(?string $mode = null)
    {
        if ($mode === null) {
            return static::$mode;
        }

        static::$mode = $mode;
    }

    /**
     * Load Aloe console and user defined commands
     */
    public static function loadConsole($externalCommands = [])
    {
        static::loadApplicationConfig();

        \Leaf\Database::connect();

        $console = new \Aloe\Console('v4.0');

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

        if (class_exists(class: 'Leaf\Queue')) {
            $externalCommands[] = \Leaf\Queue::commands();
        }

        if (class_exists('Leaf\Billing')) {
            $externalCommands[] = \Leaf\Billing::commands();
        }

        foreach ($externalCommands as $command) {
            $console->register($command);
        }

        try {
            $console->run();
        } catch (\Throwable $th) {
            echo "\n------------------------\n\nLeaf MVC ";
            $handler = (new \Leaf\Exception\Run());
            $handler->allowQuit(false);
            $handler->writeToOutput(false);
            $handler->pushHandler(new \Leaf\Exception\Handler\PlainTextHandler());

            echo $handler->handleException($th);
            return 1;
        }
    }

    /**
     * Load all application routes and run the application
     */
    public static function runApplication()
    {
        static::loadApplicationConfig();

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
