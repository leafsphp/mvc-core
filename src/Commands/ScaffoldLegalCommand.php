<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldLegalCommand extends Command
{
    protected $signature = 'scaffold:legal
        {--s|scaffold=default : Which scaffold to use for your legal pages (default/react/vue/svelte)}';
    protected $description = 'Scaffold privacy policy and terms of service pages';
    protected $help = 'Create editable privacy and terms pages';

    protected function handle()
    {
        $directory = getcwd();
        $scaffold = $this->option('scaffold');

        if (!in_array($scaffold, ['default', 'react', 'vue', 'svelte'])) {
            $this->error("Invalid scaffold $scaffold. Available scaffolds are default, react, vue, svelte.");
            return 1;
        }

        if (\Leaf\FS\File::exists("$directory/app/views/_inertia.blade.php")) {
            $content = \Leaf\FS\File::read("$directory/app/views/_inertia.blade.php");

            if (strpos($content, '.jsx') !== false) {
                $scaffold = 'react';
            } elseif (strpos($content, '.svelte') !== false) {
                $scaffold = 'svelte';
            } elseif (strpos($content, '.vue') !== false) {
                $scaffold = 'vue';
            }
        }

        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/legal/' . $scaffold,
            getcwd(),
            ['recursive' => true]
        );

        $this->info('Legal pages generated successfully.');
        $this->writeln('👉  Edit the EDIT ME sections in <comment>app/views/pages/legal</comment>, then visit <comment>/privacy</comment> and <comment>/terms</comment>');
        $this->writeln('⚠️  These templates are a starting point, not legal advice.');

        return 0;
    }
}
