<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ScaffoldAiCommand extends Command
{
    protected $signature = 'scaffold:ai';
    protected $description = 'Scaffold a streaming AI chat powered by Claude';
    protected $help = 'Create a chat page, streaming endpoint and Anthropic SDK setup';

    protected function handle()
    {
        $this->comment('Installing the Anthropic SDK...');

        if (!sprout()->composer()->install('anthropic-ai/sdk')->isSuccessful()) {
            $this->error('Failed to install anthropic-ai/sdk. Please run "composer require anthropic-ai/sdk" manually.');

            return 1;
        }

        \Leaf\FS\Directory::copy(
            __DIR__ . '/themes/ai',
            getcwd(),
            ['recursive' => true]
        );

        foreach (['.env', '.env.example'] as $envFile) {
            $path = getcwd() . "/$envFile";

            if (file_exists($path) && strpos((string) file_get_contents($path), 'ANTHROPIC_API_KEY') === false) {
                file_put_contents($path, "\nANTHROPIC_API_KEY=\n", FILE_APPEND);
            }
        }

        $this->info('AI chat generated successfully.');
        $this->writeln('👉  Add your ANTHROPIC_API_KEY to .env, then visit <comment>/ai</comment>');

        return 0;
    }
}
