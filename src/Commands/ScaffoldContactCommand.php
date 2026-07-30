<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldContactCommand extends Command
{
    protected $signature = 'scaffold:contact
        {--s|scaffold=default : Which scaffold to use for your contact form (default/react/vue/svelte)}';
    protected $description = 'Scaffold a contact form wired to leaf mail';
    protected $help = 'Create a contact page, controller and mailer';

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
            } else if (strpos($content, '.svelte') !== false) {
                $scaffold = 'svelte';
            } else if (strpos($content, '.vue') !== false) {
                $scaffold = 'vue';
            }
        }

        if (!class_exists('Leaf\Mail')) {
            $this->comment('Installing leaf mail...');

            if (!sprout()->composer()->install('leafs/mail')->isSuccessful()) {
                $this->error('Failed to install leafs/mail. Please run "composer require leafs/mail" manually.');

                return 1;
            }
        }

        if (!file_exists(getcwd() . '/config/mail.php')) {
            \Leaf\FS\Directory::copy(
                __DIR__ . '/themes/mail',
                getcwd(),
                ['recursive' => true]
            );
        }

        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/contact/' . $scaffold,
            getcwd(),
            ['recursive' => true]
        );

        foreach (['.env', '.env.example'] as $envFile) {
            $path = getcwd() . "/$envFile";

            if (file_exists($path) && strpos((string) file_get_contents($path), 'CONTACT_EMAIL') === false) {
                file_put_contents($path, "\nCONTACT_EMAIL=\n", FILE_APPEND);
            }
        }

        $this->info('Contact form generated successfully.');
        $this->writeln('👉  Set CONTACT_EMAIL (and your MAIL_ config) in .env, then visit <comment>/contact</comment>');

        return 0;
    }
}
