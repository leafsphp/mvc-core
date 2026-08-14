<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldShadcnCommand extends Command
{
    protected $signature = 'scaffold:shadcn';
    protected $description = 'Scaffold shadcn/ui for your react app';
    protected $help = 'Create support files for shadcn/ui in your react inertia app';

    protected function handle()
    {
        $isReactApp = \Leaf\FS\File::exists(getcwd() . '/app/views/_inertia.blade.php')
            && strpos(\Leaf\FS\File::read(getcwd() . '/app/views/_inertia.blade.php'), '.jsx') !== false;

        if (!$isReactApp) {
            $this->error('shadcn/ui is for React apps. Set up React first with "php leaf view:install --react".');

            return 1;
        }

        $this->comment('Scaffolding Shadcn support files...');

        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/shadcn',
            getcwd(),
            ['recursive' => true]
        );

        $this->info('Shadcn files generated successfully.');

        return 0;
    }
}
