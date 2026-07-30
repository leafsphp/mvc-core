<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldBlogCommand extends Command
{
    protected $signature = 'scaffold:blog';
    protected $description = 'Scaffold a markdown blog for your app';
    protected $help = 'Create blog routes, controller, views and a sample post';

    protected function handle()
    {
        $this->comment('Installing markdown support...');

        if (!sprout()->composer()->install('erusev/parsedown')->isSuccessful()) {
            $this->error('Failed to install erusev/parsedown. Please run "composer require erusev/parsedown" manually.');

            return 1;
        }

        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/blog',
            getcwd(),
            ['recursive' => true]
        );

        $this->info('Blog generated successfully.');
        $this->writeln('👉  Posts live in <comment>app/blog</comment> as markdown files. Visit <comment>/blog</comment> to see the sample post.');

        return 0;
    }
}
