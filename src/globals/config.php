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
    $config = [
        'commands' => 'app/console',
        'config' => 'config',
        'channels' => 'app/channels',
        'components' => 'app/components',
        'controllers' => 'app/controllers',
        'database' => 'app/database',
        'databaseStorage' => 'storage/app/db',
        'exceptions' => 'app/exceptions',
        'events' => 'app/events',
        'factories' => 'app/database/factories',
        'helpers' => 'app/helpers',
        'jobs' => 'app/jobs',
        'lib' => 'lib',
        'mail' => 'app/mailers',
        'middleware' => 'app/middleware',
        'migrations' => 'app/database/migrations',
        'models' => 'app/models',
        'routes' => 'app/routes',
        'schema' => 'app/database/schema',
        'scripts' => 'app/scripts',
        'seeds' => 'app/database/seeds',
        'services' => 'app/services',
        'storage' => 'storage',
        'utils' => 'app/utils',
        'views' => 'app/views',
        'workers' => 'app/workers',
    ];

    if (file_exists(dirname(__DIR__, 5) . '/config/paths.php')) {
        $config = array_merge($config, require dirname(__DIR__, 5) . '/config/paths.php');
    }

    return !$setting ? $config : $config[$setting];
}

/**
 * Get an auth configuration
 */
function AuthConfig($setting = null)
{
    return MvcConfig('auth', $setting);
}

/**
 * Get view configuration
 */
function ViewConfig($setting = null)
{
    return MvcConfig('view', $setting);
}

/**
 * Get database configuration
 */
function DatabaseConfig($setting = null)
{
    return MvcConfig('database', $setting);
}

/**
 * Get an auth configuration
 */
function CorsConfig($setting = null)
{
    return MvcConfig('cors', $setting);
}

/**
 * Get mail configuration
 */
function MailConfig($setting = null)
{
    return MvcConfig('mail', $setting);
}

/**
 * Get an application configuration
 */
function MvcConfig($appConfig, $setting = null)
{
    $config = \Leaf\Config::getStatic("mvc.config.$appConfig");
    return !$setting ? $config : $config[$setting];
}
