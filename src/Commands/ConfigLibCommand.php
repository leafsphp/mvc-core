<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ConfigLibCommand extends Command
{
    protected $signature = 'config:lib';
    protected $description = 'Setup Leaf MVC to use external libraries';
    protected $help = 'Setup Leaf MVC to use external libraries';

    protected function handle()
    {
        $directory = getcwd() . DIRECTORY_SEPARATOR . LibPath();

        if (!\Leaf\FS\Directory::exists($directory)) {
            \Leaf\FS\Directory::create($directory);
        }

        $this->comment('lib folder setup successfully!');

        return 0;
    }
}
