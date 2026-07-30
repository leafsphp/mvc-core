<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldBlogCommand extends Command
{
    protected $signature = 'scaffold:blog
        {--s|scaffold=default : Which scaffold to use for your blog (default/react/vue/svelte)}';
    protected $description = 'Scaffold a markdown blog for your app';
    protected $help = 'Create blog routes, controller, views and a sample post';

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

        $this->comment('Installing markdown support...');

        if (!sprout()->composer()->install('erusev/parsedown')->isSuccessful()) {
            $this->error('Failed to install erusev/parsedown. Please run "composer require erusev/parsedown" manually.');

            return 1;
        }

        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/blog/' . $scaffold,
            getcwd(),
            ['recursive' => true]
        );

        $this->info('Blog generated successfully.');
        $this->writeln('👉  Posts live in <comment>app/blog</comment> as markdown files. Visit <comment>/blog</comment> to see the sample post.');

        return 0;
    }
}
