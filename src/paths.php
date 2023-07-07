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
        return AppPaths('config') . "/$path";
    }
}

if (!function_exists('CommandsPath')) {
    /**
     * Commands directory path
     */
    function CommandsPath($path = ''): string
    {
        return AppPaths('commands') . "/$path";
    }
}

if (!function_exists('ControllersPath')) {
    /**
     * Controllers directory path
     */
    function ControllersPath($path = ''): string
    {
        return AppPaths('controllers') . "/$path";
    }
}

if (!function_exists('DatabasePath')) {
    /**
     * Database storage path
     */
    function DatabasePath($path = ''): string
    {
        return AppPaths('database') . "/$path";
    }
}

if (!function_exists('FactoriesPath')) {
    /**
     * Factories directory path
     */
    function FactoriesPath($path = ''): string
    {
        return AppPaths('factories') . "/$path";
    }
}

if (!function_exists('HelpersPath')) {
    /**
     * Helpers directory path
     */
    function HelpersPath($path = ''): string
    {
        return AppPaths('helpers') . "/$path";
    }
}

if (!function_exists('LibPath')) {
    /**
     * Helpers directory path
     */
    function LibPath($path = ''): string
    {
        return AppPaths('lib') . "/$path";
    }
}

if (!function_exists('MigrationsPath')) {
    /**
     * Migrations directory path
     */
    function MigrationsPath($path = '', bool $slash = true): string
    {
        return AppPaths('migrations', $slash) . "/$path";
    }
}

if (!function_exists('ModelsPath')) {
    /**
     * Models directory path
     */
    function ModelsPath($path = ''): string
    {
        return AppPaths('models') . "/$path";
    }
}

if (!function_exists('PublicPath')) {
    /**
     * Public directory path
     */
    function PublicPath($path = '', $slash = true): string
    {
        $IS_PUBLIC_ROOT = (strpos($_SERVER['SCRIPT_FILENAME'], '/public/') && strpos($_SERVER['REQUEST_URI'], '/public') == null);
        return ($IS_PUBLIC_ROOT ? '' : AppPaths('public', $slash)) . "/$path";
    }
}

if (!function_exists('RoutesPath')) {
    /**
     * Routes directory path
     */
    function RoutesPath($path = ''): string
    {
        return AppPaths('routes') . "/$path";
    }
}

if (!function_exists('SeedsPath')) {
    /**
     * Seeds directory path
     */
    function SeedsPath($path = ''): string
    {
        return AppPaths('seeds') . "/$path";
    }
}

if (!function_exists('StoragePath')) {
    /**
     * Storage directory path
     */
    function StoragePath($path = '', bool $slash = false): string
    {
        return AppPaths('storage', $slash) . "/$path";
    }
}

if (!function_exists('ViewsPath')) {
    /**
     * Views directory path
     */
    function ViewsPath($path = '', bool $slash = true): string
    {
        return AppPaths('views', $slash) . "/$path";
    }
}
