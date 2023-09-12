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

    protected static $config = [];


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

        Database::config(static::config('database'));
        auth()->config(static::config('auth'));

        if (php_sapi_name() !== 'cli') {
            app()->config(static::config('app'));
            app()->cors(static::config('cors'));
        }
    }

    /**
     * Load all config files defined in the config folder
     */
    protected static function loadConfig()
    {
        $configPath = static::$paths["config"];
        $configFiles = glob("$configPath/*.php");

        foreach ($configFiles as $configFile) {
            $configName = basename($configFile, ".php");
            $config = require $configFile;
            static::config($configName, $config);
        }
    }

    /**
     * Set/Get config
     * @param string|array $config The config to set/get
     * @param mixed $value The value to set config to. Ignored if $config is an array
     */
    public static function config($config = null, $value = null)
    {
        if (!$config) {
            return static::$config;
        }

        if (is_array($config)) {
            static::$config = array_merge(static::$config, $config);
            return;
        }

        if (!$value) {
            return static::$config[$config];
        }

        static::$config[$config] = $value;
    }

    /**
     * Load user defined libs
     */
    public static function loadLibs()
    {
        $libPath = static::$paths["lib"];
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
        $routePath = static::$paths["routes"];
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

        $app = \Leaf\Config::get('app.instance');
        $app->run();
    }
}
