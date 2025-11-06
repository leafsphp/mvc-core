<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;
use Psy\Shell;

class InteractCommand extends Command
{
    protected $signature = 'interact';
    protected $description = 'Interact with your application';
    protected $help = 'Interact with your application';

    protected function handle()
    {
        if (!sprout()->composer()->hasDependency('psy/psysh')) {
            $this->comment('> Installing psy/psysh...');

            if (!sprout()->composer()->install('psy/psysh')) {
                $this->writeln('<error>❌  Failed to install psy/psysh.</error>');
                return 1;
            }

            $this->info('> psy/psysh installed successfully!');
        }

        $shell = new Shell();
        $this->writeln($shell->run());

        return 0;
    }
}
