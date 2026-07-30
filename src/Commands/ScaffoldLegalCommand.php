<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldLegalCommand extends Command
{
    protected $signature = 'scaffold:legal';
    protected $description = 'Scaffold privacy policy and terms of service pages';
    protected $help = 'Create editable privacy and terms pages';

    protected function handle()
    {
        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/legal',
            getcwd(),
            ['recursive' => true]
        );

        $this->info('Legal pages generated successfully.');
        $this->writeln('👉  Edit the EDIT ME sections in <comment>app/views/pages/legal</comment>, then visit <comment>/privacy</comment> and <comment>/terms</comment>');
        $this->writeln('⚠️  These templates are a starting point, not legal advice.');

        return 0;
    }
}
