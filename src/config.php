<?php

// App

/**
 * Get app configuration
 */
function AppConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/app.php";
    return !$setting ? $config : $config[$setting];
}

// paths

/**
 * Get paths configuration
 */
function PathsConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/paths.php";
    return !$setting ? $config : $config[$setting];
}

// Auth

/**
 * Get an auth configuration
 */
function AuthConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/auth.php";
    return !$setting ? $config : $config[$setting];
}

// Views

/**
 * Get view configuration
 */
function ViewConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/view.php";
    return !$setting ? $config : $config[$setting] ?? null;
}

// Db

function DatabaseConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/database.php";
    return !$setting ? $config : $config[$setting];
}

// Cors

/**
 * Get an auth configuration
 */
function CorsConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/cors.php";
    return !$setting ? $config : $config[$setting];
}
