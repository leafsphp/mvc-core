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
}
