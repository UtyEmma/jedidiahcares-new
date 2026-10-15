<?php

namespace App\Console\Commands;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class DeployWebsite extends Command
{
    use ConfirmableTrait;

    protected $signature = 'cms:deploy
        {--force : Allow deployment in production without an interactive confirmation}
        {--dry-run : Show deployment steps without changing anything}
        {--skip-dependencies : Use already installed Composer and npm dependencies}
        {--skip-build : Use existing production client and SSR build artifacts}
        {--skip-reload : Do not signal workers, scheduler or SSR to restart}
        {--create-admin : Run the interactive administrator command after a successful deployment}
        {--without-media : Seed records without importing bundled media}
        {--no-maintenance : Do not enter maintenance mode (for an isolated release directory)}';

    protected $description = 'Prepare a new or updated website release: dependencies, builds, migrations, seeds, media and caches';

    private array $processEnvironment = [];

    public function handle(): int
    {
        if ($this->option('dry-run')) {
            $this->info('Deployment plan — no changes will be made.');
            foreach ($this->steps() as [$label]) {
                $this->line(' • '.$label);
            }
            if ($this->option('create-admin')) {
                $this->line(' • After deployment succeeds, run cms:create-admin with private terminal prompts');
            }

            return self::SUCCESS;
        }
        if ($this->option('create-admin') && (! $this->input->isInteractive() || ! Process::isTtySupported())) {
            $this->error('--create-admin requires an interactive terminal. Omit it for unattended deployments, then run php artisan cms:create-admin in a terminal.');

            return self::FAILURE;
        }
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }
        File::ensureDirectoryExists(storage_path('framework'));
        $lock = fopen(storage_path('framework/cms-deploy.lock'), 'c+');
        if (! $lock || ! flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) {
                fclose($lock);
            }
            $this->error('Another deployment is already running for this release storage directory.');

            return self::FAILURE;
        }
        $enteredMaintenance = false;
        try {
            foreach ($this->steps() as [$label, $action]) {
                $this->newLine();
                $this->info($label);
                if ($action === 'maintenance') {
                    if (! app()->isDownForMaintenance()) {
                        $this->artisan('down', ['--retry' => 60]);
                        $enteredMaintenance = true;
                    }
                } elseif ($action === 'key') {
                    $environmentFile = app()->environmentFilePath();
                    $environment = is_file($environmentFile) ? Dotenv::parse(File::get($environmentFile)) : [];
                    if (! Env::get('APP_KEY', $environment['APP_KEY'] ?? null)) {
                        if (! is_file($environmentFile)) {
                            throw new RuntimeException('Configure .env before generating the first application key.');
                        }
                        $this->artisan('key:generate', ['--force' => true]);
                        $newKey = Dotenv::parse(File::get($environmentFile))['APP_KEY'] ?? '';
                        if (! $newKey) {
                            throw new RuntimeException('Application key generation failed. Configure APP_KEY before retrying.');
                        }
                        // Do not pass the parent's original empty dotenv key to later processes.
                        $this->processEnvironment['APP_KEY'] = $newKey;
                    } else {
                        $this->line('Existing application key preserved.');
                    }
                } elseif ($action === 'build-check') {
                    foreach ([public_path('build/manifest.json'), base_path('bootstrap/ssr/ssr-manifest.json'), base_path('bootstrap/ssr/app.js')] as $file) {
                        if (! is_file($file)) {
                            throw new RuntimeException('A production build is missing: '.$file.'. Run without --skip-build.');
                        }
                    }
                } elseif ($action === 'storage') {
                    foreach (config('filesystems.links', []) as $link => $target) {
                        File::ensureDirectoryExists($target);
                        if (is_link($link) && realpath($link) === realpath($target)) {
                            continue;
                        }
                        if (file_exists($link) || is_link($link)) {
                            throw new RuntimeException('The storage link path is occupied: '.$link.'. Resolve it before retrying.');
                        }
                        File::link($target, $link);
                    }
                } elseif ($action === 'production-assets') {
                    if (app()->isProduction() && is_file(public_path('hot'))) {
                        File::delete(public_path('hot'));
                    }
                } elseif (is_array($action) && $action[0] === 'process') {
                    $process = new Process($action[1], base_path(), timeout: null);
                    $process->run(fn ($type, $buffer) => $this->output->write($buffer));
                    if (! $process->isSuccessful()) {
                        throw new RuntimeException('Deployment stopped because '.implode(' ', $action[1]).' failed.');
                    }
                } else {
                    $this->artisan($action[0], $action[1] ?? []);
                }
            }
            if ($enteredMaintenance) {
                $this->artisan('up');
            }
            $this->info('Deployment completed. Existing content, settings, staff accounts and application keys are preserved.');
            if (! $this->option('create-admin')) {
                $this->line('For the first installation, create your administrator with: php artisan cms:create-admin');
            }
            $this->line('Keep the scheduler and any SSR/queue processes under your hosting process manager.');
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            if ($enteredMaintenance) {
                $this->warn('The website remains in maintenance mode. Correct the failure and repeat this command, then run php artisan up when ready.');
            }

            return self::FAILURE;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        return $this->option('create-admin') ? $this->createAdministrator() : self::SUCCESS;
    }

    private function createAdministrator(): int
    {
        $this->newLine();
        $this->info('Create a CMS administrator');
        try {
            // Reboot against the deployed configuration and keep password entry on the terminal.
            $process = new Process([PHP_BINARY, base_path('artisan'), 'cms:create-admin'], base_path(), $this->processEnvironment, timeout: null);
            $process->setTty(true);
            $status = $process->run();
            if ($status === self::SUCCESS) {
                return self::SUCCESS;
            }
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
        }
        $this->warn('Deployment succeeded, but administrator creation did not complete. Retry with php artisan cms:create-admin.');

        return self::FAILURE;
    }

    private function artisan(string $command, array $arguments = []): void
    {
        // Fresh PHP processes pick up .env and configuration changes after config:clear.
        $parameters = [PHP_BINARY, base_path('artisan'), $command, '--no-interaction'];
        foreach ($arguments as $key => $value) {
            if ($value === true) {
                $parameters[] = $key;
            } elseif ($value !== false && $value !== null) {
                $parameters[] = $key.'='.$value;
            }
        }
        $process = new Process($parameters, base_path(), $this->processEnvironment, timeout: null);
        $process->run(fn ($type, $buffer) => $this->output->write($buffer));
        if (! $process->isSuccessful()) {
            throw new RuntimeException('Deployment stopped because '.$command.' failed.');
        }
    }

    private function steps(): array
    {
        $steps = [];
        if ($this->option('skip-build')) {
            $steps[] = ['Verify existing client and SSR builds', 'build-check'];
        }
        if (! $this->option('no-maintenance')) {
            $steps[] = ['Enter maintenance mode if not already enabled', 'maintenance'];
        }
        if (! $this->option('skip-dependencies')) {
            $steps[] = ['Install locked production PHP dependencies', ['process', ['composer', 'install', '--no-dev', '--prefer-dist', '--no-interaction', '--optimize-autoloader']]];
            if (! $this->option('skip-build')) {
                $steps[] = ['Install locked frontend build dependencies', ['process', ['npm', 'ci', '--include=dev', '--no-audit', '--no-fund']]];
            }
        }
        if (! $this->option('skip-build')) {
            $steps[] = ['Build production React client and SSR assets', ['process', ['npm', 'run', 'build']]];
        }
        foreach (['config:clear', 'route:clear', 'event:clear', 'view:clear', 'filament:optimize-clear'] as $command) {
            $steps[] = ['Clear stale bootstrap cache: '.$command, [$command]];
        }
        $steps[] = ['Generate an application key only when missing', 'key'];
        $steps[] = ['Run pending database migrations', ['migrate', ['--force' => true]]];
        $steps[] = ['Seed all collections, SEO and general settings without overwriting edits', $this->option('without-media')
            ? ['cms:import-content', ['--without-media' => true]] : ['db:seed', ['--force' => true]]];
        // $steps[] = ['Optimize existing CMS images with private backups', ['cms:optimize-images']];
        $steps[] = ['Prepare the public storage link (CMS originals stay private)', 'storage'];
        $steps[] = ['Publish Filament assets', ['filament:assets']];
        $steps[] = ['Remove development asset pointer in production', 'production-assets'];
        foreach (['config:cache', 'event:cache', 'route:cache', 'view:cache', 'filament:optimize'] as $command) {
            $steps[] = ['Cache release metadata: '.$command, [$command]];
        }

        $steps[] = ['Generate website site map', ['app:generate-sitemap']];
        
        if (! $this->option('skip-reload')) {
            $steps[] = ['Signal queue workers to restart', ['queue:restart']];
            $steps[] = ['Interrupt the previous scheduler run', ['schedule:interrupt']];
            if (config('inertia.ssr.enabled')) {
                $steps[] = ['Stop old SSR process for its supervisor to restart', ['inertia:stop-ssr', ['--graceful' => true]]];
            }
        }

        return $steps;
    }
}
