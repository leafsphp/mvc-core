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
     * Set/Get paths
     * @param string|array $paths The paths to set/get
     */
    public static function paths($paths = null)
    {
        if (!$paths) {
            return static::$paths;
        }

        static::$paths = $paths;
    }

    /**
     * Setup MVC application based on config
     */
    public static function loadApplicationConfig()
    {
        static::paths(PathsConfig());
        static::loadConfig();

        auth()->config(Config::getStatic('_auth'));

        if (php_sapi_name() !== 'cli') {
            app()->config(Config::getStatic('_app'));
            app()->cors(Config::getStatic('_cors'));

            if (class_exists('Leaf\Vite')) {
                \Leaf\Vite::config('assets', '');
                \Leaf\Vite::config('build', PublicPath('build', false));
                \Leaf\Vite::config('hotFile', trim(PublicPath('hot', false), '/'));
            }
        }
    }

    /**
     * Load all config files defined in the config folder
     */
    protected static function loadConfig()
    {
        $configPath = static::$paths['config'];
        $configFiles = glob("$configPath/*.php");

        foreach ($configFiles as $configFile) {
            $configName = basename($configFile, '.php');
            $config = require $configFile;

            \Leaf\Config::set("_$configName", $config);
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
