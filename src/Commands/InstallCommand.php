<?php

declare(strict_types=1);

namespace Givebutter\Laravel\Commands;

use Givebutter\Laravel\Commands\Concerns\RendersOutput;
use Givebutter\Laravel\GivebutterServiceProvider;
use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    use RendersOutput;

    protected $signature = 'givebutter:install';

    protected $description = 'Prepares the Givebutter client for use.';

    public function handle(): void
    {
        $this->infoColumn('Installing Givebutter for Laravel...');

        $this->copyConfig();

        $this->addEnvKeys('.env');
        $this->addEnvKeys('.env.example');

        $this->infoColumn('Installation complete, remember to add your API key to your environment file!');

        $wantsToSupport = $this->askToStarRepository();

        if ($wantsToSupport) {
            $this->openRepositoryInBrowser();
        }
    }

    private function copyConfig(): void
    {
        if (file_exists(config_path('givebutter.php'))) {
            $this->warnColumn('Config file already exists.');

            return;
        }

        $this->infoColumn('Config file created.');

        $this->callSilent('vendor:publish', [
            '--provider' => GivebutterServiceProvider::class,
        ]);
    }

    private function addEnvKeys(string $envFile): void
    {
        if (! is_writable(base_path($envFile))) {
            $this->errorMessage($envFile, 'File is not writeable.');

            return;
        }

        $fileContent = file_get_contents(base_path($envFile));

        if ($fileContent === false) {
            return;
        }

        if (str_contains($fileContent, 'GIVEBUTTER_API_KEY')) {
            $this->warnColumn("Variable already exists within '$envFile'.");

            return;
        }

        file_put_contents(base_path($envFile), PHP_EOL.'GIVEBUTTER_API_KEY='.PHP_EOL, FILE_APPEND);

        $this->infoColumn('GIVEBUTTER_API_KEY variable added.');
    }

    private function askToStarRepository(): bool
    {
        if (! $this->input->isInteractive()) {
            return false;
        }

        return $this->confirm('<options=bold>Wanna show Givebutter PHP for Laravel some love by starring it on GitHub?</>');
    }

    private function openRepositoryInBrowser(): void
    {
        $command = match (PHP_OS_FAMILY) {
            'Darwin' => 'open https://github.com/joeymckenzie/givebutter-laravel',
            'Windows' => 'start https://github.com/joeymckenzie/givebutter-laravel',
            'Linux' => 'xdg-open https://github.com/joeymckenzie/givebutter-laravel',
            default => ''
        };

        if ($command === '') {
            $this->warnColumn('Could not determine which browser to open on the OS.');
        }

        exec($command);
    }
}
