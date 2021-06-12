<?php

// App paths as callable methods

if (!function_exists('app_paths')) {
    /**
     * Get all app paths
     */
    function app_paths($path = null, bool $slash = false)
    {
        $paths = Leaf\Core::paths();
        $res = !$path ? $paths : $paths[$path] ?? "/";
        return $slash ? "/$res" : $res;
    }
}

if (!function_exists('config_path')) {
    /**
     * Config directory path
     */
    function config_path($path = null)
    {
        return app_paths("config_path") . "/$path";
    }
}

if (!function_exists('commands_path')) {
    /**
     * Commands directory path
     */
    function commands_path($path = null)
    {
        return app_paths("commands_path") . "/$path";
    }
}

if (!function_exists('controllers_path')) {
    /**
     * Controllers directory path
     */
    function controllers_path($path = null)
    {
        return app_paths("controllers_path") . "/$path";
    }
}

if (!function_exists('database_path')) {
    /**
     * Database storage path
     */
    function database_path($path = null)
    {
        return app_paths("database_storage_path") . "/$path";
    }
}

if (!function_exists('factories_path')) {
    /**
     * Factories directory path
     */
    function factories_path($path = null)
    {
        return app_paths("factories_path") . "/$path";
    }
}

if (!function_exists('helpers_path')) {
    /**
     * Helpers directory path
     */
    function helpers_path($path = null)
    {
        return app_paths("helpers_path") . "/$path";
    }
}

if (!function_exists('lib_path')) {
    /**
     * Helpers directory path
     */
    function lib_path($path = null)
    {
        return app_paths("lib_path") . "/$path";
    }
}

if (!function_exists('migrations_path')) {
    /**
     * Migrations directory path
     */
    function migrations_path($path = null, bool $slash = true)
    {
        return app_paths("migrations_path", $slash) . "/$path";
    }
}

if (!function_exists('models_path')) {
    /**
     * Models directory path
     */
    function models_path($path = null)
    {
        return app_paths("models_path") . "/$path";
    }
}

if (!function_exists('public_path')) {
    /**
     * Public directory path
     */
    function public_path($path = null)
    {
        return app_paths("public_path") . "/$path";
    }
}

if (!function_exists('routes_path')) {
    /**
     * Routes directory path
     */
    function routes_path($path = null)
    {
        return app_paths("routes_path") . "/$path";
    }
}

if (!function_exists('seeds_path')) {
    /**
     * Seeds directory path
     */
    function seeds_path($path = null)
    {
        return app_paths("seeds_path") . "/$path";
    }
}

if (!function_exists('storage_path')) {
    /**
     * Storage directory path
     */
    function storage_path($path = null, bool $slash = false)
    {
        return app_paths("storage_path", $slash) . "/$path";
    }
}

if (!function_exists('views_path')) {
    /**
     * Views directory path
     */
    function views_path($path = null, bool $slash = true)
    {
        return app_paths("views_path", $slash) . "/$path";
    }
}
