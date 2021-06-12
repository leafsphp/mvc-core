<?php

// App paths as callable methods

/**
 * Get all app paths
 */
function app_paths($path = null, bool $slash = false)
{
	$paths = require __DIR__ . "/paths.php";
	$res = !$path ? $paths : $paths[$path] ?? "/";
	return $slash ? "/$res" : $res;
}

/**
 * Views directory path
 */
function views_path($path = null, bool $slash = true)
{
	return app_paths("views_path", $slash) . "/$path";
}

/**
 * Config directory path
 */
function config_path($path = null)
{
	return app_paths("config_path") . "/$path";
}

/**
 * Storage directory path
 */
function storage_path($path = null, bool $slash = false)
{
	return app_paths("storage_path", $slash) . "/$path";
}

/**
 * Commands directory path
 */
function commands_path($path = null)
{
	return app_paths("commands_path") . "/$path";
}

/**
 * Controllers directory path
 */
function controllers_path($path = null)
{
	return app_paths("controllers_path") . "/$path";
}

/**
 * Models directory path
 */
function models_path($path = null)
{
	return app_paths("models_path") . "/$path";
}

/**
 * Migrations directory path
 */
function migrations_path($path = null, bool $slash = true)
{
	return app_paths("migrations_path", $slash) . "/$path";
}

/**
 * Seeds directory path
 */
function seeds_path($path = null)
{
	return app_paths("seeds_path") . "/$path";
}

/**
 * Factories directory path
 */
function factories_path($path = null)
{
	return app_paths("factories_path") . "/$path";
}

/**
 * Routes directory path
 */
function routes_path($path = null)
{
	return app_paths("routes_path") . "/$path";
}

/**
 * Helpers directory path
 */
function helpers_path($path = null)
{
	return app_paths("helpers_path") . "/$path";
}

/**
 * Helpers directory path
 */
function lib_path($path = null)
{
	return app_paths("lib_path") . "/$path";
}

/**
 * Public directory path
 */
function public_path($path = null)
{
	return app_paths("public_path") . "/$path";
}

/**
 * Database storage path
 */
function database_path($path = null)
{
	return app_paths("database_storage_path") . "/$path";
}
