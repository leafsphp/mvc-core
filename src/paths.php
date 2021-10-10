<?php

if (!function_exists('AppPaths')) {
    /**
     * Get all app paths
     */
    function AppPaths($path = null, bool $slash = false)
    {
        $paths = Leaf\Core::paths();
        $res = !$path ? $paths : $paths[$path] ?? "/";
        return $slash ? "/$res" : $res;
    }
}

if (!function_exists('ConfigPath')) {
    /**
     * Config directory path
     */
    function ConfigPath($path = null)
    {
        return AppPaths("configPath") . "/$path";
    }
}

if (!function_exists('CommandsPath')) {
    /**
     * Commands directory path
     */
    function CommandsPath($path = null)
    {
        return AppPaths("commandsPath") . "/$path";
    }
}

if (!function_exists('ControllersPath')) {
    /**
     * Controllers directory path
     */
    function ControllersPath($path = null)
    {
        return AppPaths("controllersPath") . "/$path";
    }
}

if (!function_exists('DatabasePath')) {
    /**
     * Database storage path
     */
    function DatabasePath($path = null)
    {
        return AppPaths("database_storagePath") . "/$path";
    }
}

if (!function_exists('FactoriesPath')) {
    /**
     * Factories directory path
     */
    function FactoriesPath($path = null)
    {
        return AppPaths("factoriesPath") . "/$path";
    }
}

if (!function_exists('HelpersPath')) {
    /**
     * Helpers directory path
     */
    function HelpersPath($path = null)
    {
        return AppPaths("helpersPath") . "/$path";
    }
}

if (!function_exists('LibPath')) {
    /**
     * Helpers directory path
     */
    function LibPath($path = null)
    {
        return AppPaths("libPath") . "/$path";
    }
}

if (!function_exists('MigrationsPath')) {
    /**
     * Migrations directory path
     */
    function MigrationsPath($path = null, bool $slash = true)
    {
        return AppPaths("migrationsPath", $slash) . "/$path";
    }
}

if (!function_exists('ModelsPath')) {
    /**
     * Models directory path
     */
    function ModelsPath($path = null)
    {
        return AppPaths("modelsPath") . "/$path";
    }
}

if (!function_exists('PublicPath')) {
    /**
     * Public directory path
     */
    function PublicPath($path = null)
    {
        return AppPaths("publicPath") . "/$path";
    }
}

if (!function_exists('RoutesPath')) {
    /**
     * Routes directory path
     */
    function RoutesPath($path = null)
    {
        return AppPaths("routesPath") . "/$path";
    }
}

if (!function_exists('SeedsPath')) {
    /**
     * Seeds directory path
     */
    function SeedsPath($path = null)
    {
        return AppPaths("seedsPath") . "/$path";
    }
}

if (!function_exists('StoragePath')) {
    /**
     * Storage directory path
     */
    function StoragePath($path = null, bool $slash = false)
    {
        return AppPaths("storagePath", $slash) . "/$path";
    }
}

if (!function_exists('ViewsPath')) {
    /**
     * Views directory path
     */
    function ViewsPath($path = null, bool $slash = true)
    {
        return AppPaths("viewsPath", $slash) . "/$path";
    }
}
