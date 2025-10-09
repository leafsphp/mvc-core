<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class EnvSetCommand extends Command
{
    protected $signature = 'env:set
        {key : The environment variable key}
        {value : The environment variable value}';
    protected $description = 'Set a new environment variable for your app';
    protected $help = 'Set a new environment variable in the .env file and update .env.example if it exists.';

    protected function handle()
    {
        $envFile = getcwd() . DIRECTORY_SEPARATOR . '.env';

        if (!file_exists($envFile)) {
            $this->comment('No .env file found. Generating one...');

            if (sprout()->process('php leaf env:generate')->run() !== 0) {
                $this->error('Couldn\'t generate .env file. Please run `php leaf env:generate` first.');
                return 1;
            }
        }

        \Leaf\FS\File::write($envFile, function ($env) {
            $key = $this->argument('key');
            $value = $this->argument('value');

            if (strpos($env, $key) !== false) {
                $this->info("$key already exists, updating value...");
                $env = preg_replace("/^$key=(.*)/m", "$key=$value", $env);
            } else {
                $this->info("Setting new environment variable: $key");
                $env .= "\n$key=$value\n";
            }

            return $env;
        });

        if (file_exists($envExampleFile = getcwd() . DIRECTORY_SEPARATOR . '.env.example')) {
            \Leaf\FS\File::write($envExampleFile, function ($envExample) {
                $key = $this->argument('key');

                if (strpos($envExample, $key) === false) {
                    $envExample .= "\n$key=\n";
                }

                return $envExample;
            });
        }

        $this->info("Environment updated successfully.");

        return 0;
    }
}
