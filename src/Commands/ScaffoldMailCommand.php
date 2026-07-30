<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldMailCommand extends Command
{
    protected $signature = 'scaffold:mail';
    protected $description = 'Install leaf mail and setup mail config';
    protected $help = 'Install leaf mail and setup mail config';

    protected function handle()
    {
        $this->comment('Installing leaf mail...');

        if (!sprout()->composer()->install('leafs/mail')->isSuccessful()) {
            $this->error('Failed to install leafs/mail. Please run "composer require leafs/mail" manually.');

            return 1;
        }

        $this->comment('Setting up leaf mail...');

        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/mail',
            getcwd(),
            ['recursive' => true]
        );

        $this->info('Leaf mail installed successfully!');

        return 0;
    }
}
