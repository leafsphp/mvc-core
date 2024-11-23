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

        auth()->config(Config::getStatic('mvc.config.auth'));

        if (php_sapi_name() !== 'cli') {
            app()->config(Config::getStatic('mvc.config.app'));
            app()->cors(Config::getStatic('mvc.config.cors'));

            if (class_exists('Leaf\Anchor\CSRF')) {
                $csrfEnabled = (
                    Config::getStatic('mvc.config.csrf') &&
                    Config::getStatic('mvc.config.auth')['session'] ?? false
                );

                if (Config::getStatic('mvc.config.csrf')['enabled'] ?? null !== null) {
                    $csrfEnabled = Config::getStatic('mvc.config.csrf')['enabled'];
                }

                app()->csrf(Config::getStatic('mvc.config.csrf'));
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
     * Load all application routes and run the application
     */
    public static function runApplication()
    {
        $routePath = static::$paths['routes'];
        $routeFiles = glob("$routePath/*.php");

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
