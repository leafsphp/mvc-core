<?php

// App

/**
 * Get app configuration
 */
function AppConfig($setting = null)
{
    $config = require dirname(__DIR__, 4) . '/config/app.php';
    return !$setting ? $config : $config[$setting];
}

// paths

/**
 * Get paths configuration
 */
function PathsConfig($setting = null)
{
    $config = require dirname(__DIR__, 4) . '/config/paths.php';
    return !$setting ? $config : $config[$setting];
}

// Auth

/**
 * Get an auth configuration
 */
function AuthConfig($setting = null)
{
    $config = require dirname(__DIR__, 4) . '/config/auth.php';
    return !$setting ? $config : $config[$setting];
}

// Views

/**
 * Get view configuration
 */
function ViewConfig($setting = null)
{
    $config = require dirname(__DIR__, 4) . '/config/view.php';
    return !$setting ? $config : $config[$setting] ?? null;
}

// Db

function DatabaseConfig($setting = null)
{
    $config = require dirname(__DIR__, 4) . '/config/database.php';
    return !$setting ? $config : $config[$setting];
}

// Cors

/**
 * Get an auth configuration
 */
function CorsConfig($setting = null)
{
    $config = require dirname(__DIR__, 4) . '/config/cors.php';
    return !$setting ? $config : $config[$setting];
}
