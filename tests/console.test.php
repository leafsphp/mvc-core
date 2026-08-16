<?php

// Leaf MVC console, tested from mvc-core against a throwaway mini-app —
// these are the leafMVC template's tests, living here to keep installs lean.

beforeEach(function () {
    $this->sandbox = sandboxApp();
});

afterEach(function () {
    removeSandbox($this->sandbox);
});

test('the console boots and lists commands', function () {
    [$exit, $output] = mvc($this->sandbox, 'list');

    expect($exit)->toBe(0)
        ->and($output)->toContain('g:controller')
        ->and($output)->toContain('scaffold:auth') // db:* arrives with the schema module
        ->and(substr_count($output, 'scaffold:auth'))->toBe(1); // no duplicate registrations
});

test('unknown commands exit non-zero', function () {
    [$exit] = mvc($this->sandbox, 'not-a-command');

    expect($exit)->toBe(1);
});

test('g:controller generates a controller class', function () {
    [$exit] = mvc($this->sandbox, 'g:controller Posts');

    $file = $this->sandbox . '/app/controllers/PostsController.php';

    expect($exit)->toBe(0)
        ->and(file_exists($file))->toBeTrue()
        ->and(file_get_contents($file))->toContain('class PostsController extends Controller');
});

test('d:controller deletes a generated controller', function () {
    mvc($this->sandbox, 'g:controller Trash');

    [$exit] = mvc($this->sandbox, 'd:controller Trash');

    expect($exit)->toBe(0)
        ->and(file_exists($this->sandbox . '/app/controllers/TrashController.php'))->toBeFalse();
});

test('g:template generates a blade view', function () {
    [$exit] = mvc($this->sandbox, 'g:template dashboard');

    expect($exit)->toBe(0)
        ->and(file_exists($this->sandbox . '/app/views/dashboard.blade.php'))->toBeTrue();
});

test('env:set writes to the env file', function () {
    [$exit] = mvc($this->sandbox, 'env:set TEST_FLAG=on');

    expect($exit)->toBe(0)
        ->and(file_get_contents($this->sandbox . '/.env'))->toContain('TEST_FLAG=on');
});

test('lib files load for console commands', function () {
    // https://github.com/leafsphp/sprout/issues/4 — regression
    file_put_contents($this->sandbox . '/lib/helpers.php', "<?php\n\nfunction sandbox_lib_loaded(): string\n{\n    return 'lib loaded in cli';\n}\n");
    file_put_contents($this->sandbox . '/app/console/LibProbeCommand.php', <<<'PHP'
<?php

namespace App\Console;

use Leaf\Sprout\Command;

class LibProbeCommand extends Command
{
    protected $signature = 'lib:probe';
    protected $description = 'Probe lib loading';

    protected function handle(): int
    {
        $this->info(function_exists('sandbox_lib_loaded') ? sandbox_lib_loaded() : 'LIBS NOT LOADED');

        return 0;
    }
}
PHP);

    [$exit, $output] = mvc($this->sandbox, 'lib:probe');

    expect($exit)->toBe(0)
        ->and($output)->toContain('lib loaded in cli');
});

test('scaffold:shadcn refuses to run outside a react app', function () {
    $sandbox = sandboxApp();

    [$exit, $output] = mvc($sandbox, 'scaffold:shadcn');

    expect($exit)->toBe(1);
    expect($output)->toContain('React');

    removeSandbox($sandbox);
});

test('view:install pins every vite-adjacent npm package and checks results', function () {
    $source = file_get_contents(dirname(__DIR__) . '/src/Commands/ViewInstallCommand.php');

    // unpinned @vitejs/* and vite resolve to whatever npm's latest is —
    // when vite 8 shipped, latest plugin-react moved to a peer range
    // @leafphp/vite-plugin doesn't allow, and every install ERESOLVE'd
    preg_match_all('/->install\(\'([^\']+)\'\)/', $source, $matches);

    foreach ($matches[1] as $packageList) {
        foreach (explode(' ', $packageList) as $package) {
            expect($package)->not->toBe('npm')->not->toBe('install');

            if (preg_match('/^(@vitejs\/|@sveltejs\/|@inertiajs\/|vite$)/', $package)) {
                expect($package)->toContain('@^');
            }
        }
    }

    // install() returns a Process (always truthy) — a bare boolean guard
    // reports success even when npm exits non-zero
    preg_match_all('/!\s*sprout\(\)->(?:npm|composer)\(\)->install\(\'[^\']+\'\)\s*[\)&|]/', $source, $bareGuards);

    expect($bareGuards[0])->toBeEmpty();
});

test('db() borrows the Eloquent connection instead of opening its own', function () {
    // two PDO connections to one sqlite file is how an auth read used to
    // deadlock the first model write — LEAF-132
    file_put_contents($this->sandbox . '/app/console/ConnectionProbeCommand.php', <<<'PHP'
<?php

namespace App\Console;

use Leaf\Sprout\Command;

class ConnectionProbeCommand extends Command
{
    protected $signature = 'connection:probe';
    protected $description = 'Probe db()/Eloquent connection identity';

    protected function handle(): int
    {
        $leafDbPdo = db()->connection();
        $eloquentPdo = \Leaf\Database::$capsule->getConnection()->getPdo();

        $this->info($leafDbPdo === $eloquentPdo ? 'one shared connection' : 'SEPARATE CONNECTIONS');

        // and the shared connection actually works from both sides
        $eloquentPdo->exec('CREATE TABLE IF NOT EXISTS probes (name TEXT)');
        $eloquentPdo->exec("INSERT INTO probes VALUES ('via-eloquent')");
        $row = db()->query('SELECT name FROM probes')->fetchObj();
        $this->info('read back: ' . $row->name);

        // a db() transaction must cover Eloquent-side writes now — this
        // was impossible on two connections (the old docs caveat)
        db()->beginTransaction();
        \Leaf\Database::$capsule::table('probes')->insert(['name' => 'rolled-back']);
        db()->rollback();

        $count = \Leaf\Database::$capsule::table('probes')->where('name', 'rolled-back')->count();
        $this->info($count === 0 ? 'transaction covers models' : 'TRANSACTION LEAKED');

        return 0;
    }
}
PHP);

    [$exit, $output] = mvc($this->sandbox, 'connection:probe');

    expect($exit)->toBe(0)
        ->and($output)->toContain('one shared connection')
        ->and($output)->toContain('read back: via-eloquent')
        ->and($output)->toContain('transaction covers models');
});

test('global CLI commands get a helpful hint instead of a bare not-found', function () {
    [$exit, $output] = mvc($this->sandbox, 'install auth');

    expect($exit)->toBe(1)
        ->and($output)->toContain('global Leaf CLI command')
        ->and($output)->toContain('leaf install');
});
