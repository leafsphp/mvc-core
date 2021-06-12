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
function viewConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/view.php";
    return !$setting ? $config : $config[$setting];
}

// Db

function dbConfig($setting = null)
{
    $config = require __DIR__ . "/../../../../Config/database.php";
    return !$setting ? $config : $config[$setting];
}
