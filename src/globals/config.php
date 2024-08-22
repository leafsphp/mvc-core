<?php

/**
 * Get app configuration
 */
function AppConfig($setting = null)
{
    return MvcConfig('app', $setting);
}

/**
 * Get paths configuration
 */
function PathsConfig($setting = null)
{
    $config = require dirname(__DIR__, 5) . '/config/paths.php';
    return !$setting ? $config : $config[$setting];
}

/**
 * Get an auth configuration
 */
function AuthConfig($setting = null)
{
    return MvcConfig('_auth', $setting);
}

/**
 * Get view configuration
 */
function ViewConfig($setting = null)
{
    return MvcConfig('_view', $setting);
}

/**
 * Get database configuration
 */
function DatabaseConfig($setting = null)
{
    return MvcConfig('_database', $setting);
}

/**
 * Get an auth configuration
 */
function CorsConfig($setting = null)
{
    return MvcConfig('_cors', $setting);
}

/**
 * Get mail configuration
 */
function MailConfig($setting = null)
{
    return MvcConfig('_mail', $setting);
}

/**
 * Get an application configuration
 */
function MvcConfig($appConfig, $setting = null)
{
    $config = \Leaf\Core::config($appConfig);
    return !$setting ? $config : $config[$setting];
}
