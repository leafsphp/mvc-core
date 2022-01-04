<?php

if (!function_exists('AppPaths')) {
    /**
     * Get all app paths
     */
    function AppPaths($path = null, bool $slash = false)
    {
        $paths = Leaf\Core::paths();
        $res = !$path ? $paths : $paths[$path] ?? '/';
        return $slash ? "/$res" : $res;
    }
}

if (!function_exists('ConfigPath')) {
    /**
     * Config directory path
     */
    function ConfigPath($path = ''): string
    {
        return AppPaths('configPath') . "/$path";
    }
}

if (!function_exists('CommandsPath')) {
    /**
     * Commands directory path
     */
    function CommandsPath($path = ''): string
    {
        return AppPaths('commandsPath') . "/$path";
    }
}

if (!function_exists('ControllersPath')) {
    /**
     * Controllers directory path
     */
    function ControllersPath($path = ''): string
    {
        return AppPaths('controllersPath') . "/$path";
    }
}

if (!function_exists('DatabasePath')) {
    /**
     * Database storage path
     */
    function DatabasePath($path = ''): string
    {
        return AppPaths('databaseStoragePath') . "/$path";
    }
}

if (!function_exists('FactoriesPath')) {
    /**
     * Factories directory path
     */
    function FactoriesPath($path = ''): string
    {
        return AppPaths('factoriesPath') . "/$path";
    }
}

if (!function_exists('HelpersPath')) {
    /**
     * Helpers directory path
     */
    function HelpersPath($path = ''): string
    {
        return AppPaths('helpersPath') . "/$path";
    }
}

if (!function_exists('LibPath')) {
    /**
     * Helpers directory path
     */
    function LibPath($path = ''): string
    {
        return AppPaths('libPath') . "/$path";
    }
}

if (!function_exists('MigrationsPath')) {
    /**
     * Migrations directory path
     */
    function MigrationsPath($path = '', bool $slash = true): string
    {
        return AppPaths('migrationsPath', $slash) . "/$path";
    }
}

if (!function_exists('ModelsPath')) {
    /**
     * Models directory path
     */
    function ModelsPath($path = ''): string
    {
        return AppPaths('modelsPath') . "/$path";
    }
}

if (!function_exists('PublicPath')) {
    /**
     * Public directory path
     */
    function PublicPath($path = '', $slash = true): string
    {
        $IS_PUBLIC_ROOT = (strpos($_SERVER['SCRIPT_FILENAME'], '/public/') && strpos($_SERVER['REQUEST_URI'], '/public') == null);
        return ($IS_PUBLIC_ROOT ? '' : AppPaths('publicPath', $slash)) . "/$path";
    }
}

if (!function_exists('RoutesPath')) {
    /**
     * Routes directory path
     */
    function RoutesPath($path = ''): string
    {
        return AppPaths('routesPath') . "/$path";
    }
}

if (!function_exists('SeedsPath')) {
    /**
     * Seeds directory path
     */
    function SeedsPath($path = ''): string
    {
        return AppPaths('seedsPath') . "/$path";
    }
}

if (!function_exists('StoragePath')) {
    /**
     * Storage directory path
     */
    function StoragePath($path = '', bool $slash = false): string
    {
        return AppPaths('storagePath', $slash) . "/$path";
    }
}

if (!function_exists('ViewsPath')) {
    /**
     * Views directory path
     */
    function ViewsPath($path = '', bool $slash = true): string
    {
        return AppPaths('viewsPath', $slash) . "/$path";
    }
}
