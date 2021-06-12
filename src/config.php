<?php

// App

/**
 * Get app configuration
 */
function AppConfig($setting = null)
{
    $config = require __DIR__ . "/app.php";
    return !$setting ? $config : $config[$setting];
}

// Auth

/**
 * Get an auth configuration
 */
function AuthConfig($setting = null)
{
    $config = require __DIR__ . "/auth.php";
    return !$setting ? $config : $config[$setting];
}

// Views

/**
 * Get view configuration
 */
function viewConfig($setting = null)
{
    $config = require __DIR__ . "/view.php";
    return !$setting ? $config : $config[$setting];
}

// Db

function dbConfig($setting = null)
{
    $config = require __DIR__ . "/database.php";
    return !$setting ? $config : $config[$setting];
}
