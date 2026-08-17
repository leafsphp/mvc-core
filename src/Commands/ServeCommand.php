<?php

namespace Leaf\Commands;

use Leaf\Sprout\Command;

class ServeCommand extends Command
{
    protected $signature = 'serve
        {--p|port=5500 : Port to run Leaf app on}
        {--t|path? : Path to your app}
        {--s|host=localhost : Your application host}
        {--c|clean? : Run PHP server without Vite/Redis/queue processes}
        {--w|no-env-watch? : Run PHP server without automatic .env file watching}';
    protected $description = 'Start the leaf development server';
    protected $help = 'Run your Leaf app on PHP\'s local development server';

    protected $host;
    protected $port;
    protected $path;

    /**
     * Companion processes (vite, redis, queue worker) running beside the server
     * @var array<int, \Leaf\Sprout\Process>
     */
    protected $companions = [];

    protected function handle()
    {
        $redisDetected = class_exists('Leaf\Redis');
        $jobsDetected = class_exists('Leaf\Job') && file_exists(getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'jobs');
        $viteDetected = (class_exists('Leaf\Vite') || file_exists(getcwd() . DIRECTORY_SEPARATOR . 'vite.config.js')) && file_exists(getcwd() . DIRECTORY_SEPARATOR . 'package.json');

        $this->port = $this->option('port');
        $this->path = $this->option('path') ?: getcwd() . DIRECTORY_SEPARATOR . 'public';
        $this->host = $this->option('host');

        if (!is_dir($this->path)) {
            $this->error("Directory {$this->path} does not exist");
            return 1;
        }

        $this->writeln("<comment> _                __   __  ____     ______
| |    ___  __ _ / _| |  \/  \ \   / / ___|
| |   / _ \/ _` | |_  | |\/| |\ \ / / |
| |__|  __/ (_| |  _| | |  | | \ V /| |___
|_____\___|\__,_|_|   |_|  |_|  \_/  \____|\n</comment>");

        $maxPortsToCheck = 50;
        $portsChecked = 0;

        while ($portsChecked < $maxPortsToCheck) {
            $portsChecked++;

            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 0.1);

            if ($socket) {
                fclose($socket);
                $this->writeln("<info> > </info>Port {$this->port} is already in use, trying port " . ($this->port + 1) . '...');
                $this->port++;
            } else {
                break;
            }
        }

        if ($portsChecked >= $maxPortsToCheck) {
            $this->error("Could not find an available port after $maxPortsToCheck attempts");
            return 1;
        }

        if (\Leaf\FS\File::exists(getcwd() . '/.env')) {
            \Leaf\FS\File::write(getcwd() . '/.env', function ($content) {
                $content = preg_replace('/APP_URL=(.*)/', "APP_URL=http://{$this->host}:{$this->port}", $content);
                $content = preg_replace('/APP_PORT=(.*)/', "APP_PORT={$this->port}", $content);

                return $content;
            });
        }

        if (!$this->option('clean')) {
            if ($viteDetected) {
                $this->writeln('<info> > </info>Vite detected → starting Vite server alongside');
                $this->startCompanion('vite', 'npm run dev', '0;35');
            }

            if ($redisDetected) {
                $this->startRedisIfNeeded();
            }

            if ($jobsDetected) {
                $this->writeln('<info> > </info>Jobs detected → starting queue worker alongside');
                $this->startCompanion('queue', 'php leaf queue:work', '0;33');
            }
        }

        $this->info("\nHappy gardening 🍁\n");

        $watchEnv = !$this->option('no-env-watch') && file_exists(getcwd() . DIRECTORY_SEPARATOR . '.env');

        $exitCode = $watchEnv ? $this->runServerWithEnvWatch() : $this->runServer();

        foreach ($this->companions as $companion) {
            $companion->stop();
        }

        return $exitCode;
    }

    /**
     * Run the PHP server in the foreground until it exits
     */
    protected function runServer(): int
    {
        $server = sprout()
            ->process($this->buildPhpServerCommand())
            ->setTimeout(null);

        $server->start($this->prefixedEcho('server', '0;36'));

        while ($server->isRunning()) {
            usleep(500000);
            $this->pumpCompanions();
        }

        $this->pumpCompanions();

        return (int) $server->getExitCode();
    }

    /**
     * Run the PHP server, restarting it whenever .env changes.
     * A plain mtime poll: no node, no npx, works the same on Windows.
     */
    protected function runServerWithEnvWatch(): int
    {
        $envFile = getcwd() . DIRECTORY_SEPARATOR . '.env';

        // the APP_URL rewrite above already touched .env in this process:
        // drop the stat cache or the first poll sees a stale mtime and
        // restarts the server immediately
        clearstatcache(true, $envFile);
        $lastMtime = @filemtime($envFile);

        while (true) {
            $server = sprout()
                ->process($this->buildPhpServerCommand())
                ->setTimeout(null);

            $server->start($this->prefixedEcho('server', '0;36'));

            $restart = false;

            while ($server->isRunning()) {
                usleep(500000);

                $this->pumpCompanions();

                clearstatcache(true, $envFile);
                $mtime = @filemtime($envFile);

                if ($mtime !== false && $lastMtime !== false && $mtime !== $lastMtime) {
                    $lastMtime = $mtime;
                    $restart = true;
                    $this->writeln('<info> > </info>.env changed → restarting server...');
                    $server->stop();

                    break;
                }

                $lastMtime = $mtime === false ? $lastMtime : $mtime;
            }

            if (!$restart) {
                return (int) $server->getExitCode();
            }
        }
    }

    /**
     * Flush any buffered companion output through their prefixed callbacks.
     * Output callbacks only fire when a process object's pipes are read —
     * an untouched companion never prints a single line
     */
    protected function pumpCompanions()
    {
        foreach ($this->companions as $companion) {
            $companion->isRunning();
        }
    }

    /**
     * Start a companion process with prefixed output
     */
    protected function startCompanion(string $name, string $command, string $color)
    {
        $process = sprout()->process($command)->setTimeout(null);

        $process->start($this->prefixedEcho($name, $color));

        $this->companions[] = $process;

        return $process;
    }

    /**
     * Output callback that tags every line with a colored [name] label.
     * Process output arrives in arbitrary chunks — multi-line buffers and
     * split lines both — so lines are reassembled before tagging, else
     * only a chunk's first line gets the label and the rest float loose
     */
    protected function prefixedEcho(string $name, string $color): callable
    {
        $carry = '';

        return function ($type, $buffer) use ($name, $color, &$carry) {
            $carry .= $buffer;
            $lines = explode("\n", $carry);
            $carry = array_pop($lines); // an unterminated line waits for its chunk

            foreach ($lines as $line) {
                echo "\033[{$color}m[$name]\033[0m $line\n";
            }
        };
    }

    /**
     * Start a local redis server when one isn't already reachable
     */
    protected function startRedisIfNeeded()
    {
        $redisHost = _env('REDIS_HOST', '127.0.0.1');
        $redisPort = _env('REDIS_PORT', 6379);

        if (strpos($redisHost, 'tls://') !== false || strpos($redisHost, 'rediss://') !== false) {
            $this->writeln('<info> > </info>Managed Redis detected (TLS) → skipping embedded server startup');

            return;
        }

        $socket = @fsockopen($redisHost, (int) $redisPort, $errno, $errstr, 0.5);

        if ($socket) {
            fclose($socket);
            $this->writeln("<info> > </info>Redis detected at $redisHost:$redisPort → already running, skipping embedded server startup");

            return;
        }

        $this->writeln('<info> > </info>Redis detected (local) → starting embedded Redis server');

        \Leaf\FS\Directory::create(getcwd() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'database');
        $this->startCompanion('redis', 'redis-server --dir ' . escapeshellarg('storage' . DIRECTORY_SEPARATOR . 'database'), '0;31');
    }

    /**
     * Build PHP server command with proper escaping for the platform
     */
    protected function buildPhpServerCommand()
    {
        $prefix = DIRECTORY_SEPARATOR === '\\' ? '' : 'exec ';

        return "{$prefix}php -S {$this->host}:{$this->port} -t " . escapeshellarg($this->path);
    }
}
