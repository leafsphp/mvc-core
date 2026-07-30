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
