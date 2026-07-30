<?php

/*
|--------------------------------------------------------------------------
| MVC Core test helpers
|--------------------------------------------------------------------------
|
| Leaf MVC's tests live here (not in the leafMVC template, to keep installs
| lean). Console tests run against a throwaway mini-app scaffolded per run.
|
*/

define('MVC_CORE_ROOT', dirname(__DIR__));

function sandboxApp(): string
{
    $sandbox = '/tmp/mvc-core-test-' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 10);

    foreach (['app/controllers', 'app/models', 'app/views', 'app/views/errors', 'app/routes', 'app/console', 'app/database', 'lib', 'public', 'storage/app/public', 'storage/framework/views', 'storage/logs'] as $dir) {
        mkdir("$sandbox/$dir", 0777, true);
    }

    file_put_contents("$sandbox/.env", "APP_ENV=testing\nAPP_DEBUG=true\nDB_CONNECTION=sqlite\nDB_DATABASE=$sandbox/storage/db.sqlite\n");
    touch("$sandbox/storage/db.sqlite");

    // the app's `leaf` bin, pointed at mvc-core's vendor, with the App\
    // autoload mapping leafMVC's composer.json normally provides
    $bin = implode("\n", [
        '#!/usr/bin/env php',
        '<?php',
        'chdir(__DIR__);',
        "require '" . MVC_CORE_ROOT . "/vendor/autoload.php';",
        '',
        'spl_autoload_register(function ($class) {',
        "    if (strpos(\$class, 'App\\\\') === 0) {",
        "        \$parts = explode('\\\\', substr(\$class, 4));",
        '        $file = array_pop($parts);',
        "        \$dir = strtolower(implode('/', \$parts));",
        "        \$path = __DIR__ . '/app/' . (\$dir ? \$dir . '/' : '') . \$file . '.php';",
        '',
        '        if (file_exists($path)) {',
        '            require $path;',
        '        }',
        '    }',
        '});',
        '',
        'try {',
        '    \\Dotenv\\Dotenv::createUnsafeImmutable(__DIR__)->load();',
        '} catch (\\Throwable $th) {',
        '}',
        '',
        'Leaf\\Core::loadConsole();',
    ]);

    file_put_contents("$sandbox/leaf", $bin);

    return $sandbox;
}

function mvc(string $sandbox, string $command): array
{
    exec('cd ' . escapeshellarg($sandbox) . ' && ' . escapeshellarg(PHP_BINARY) . " leaf $command 2>&1", $output, $exit);

    return [$exit, implode("\n", $output)];
}

function removeSandbox(string $dir): void
{
    if (is_dir($dir)) {
        exec('rm -rf ' . escapeshellarg($dir));
    }
}

// Boot the shared app instance before the suite starts so Leaf's error
// handler is registered outside of any test — keeps PHPUnit's
// handler-stack checks happy (same pattern as leaf core's suite).
app();
