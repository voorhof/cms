<?php

namespace Voorhof\Cms\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Bries and CMS Default Installation Command
 *
 * @property string $signature Command signature
 * @property string $description Command description
 */
#[AsCommand(name: 'cms:bries')]
class InstallBriesCmsCommand extends Command
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'cms:bries';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Install the CMS and Bries with default options.';

    /**
     * Install the CMS and Bries components.
     *
     * @return int|null 0 on success, 1 on failure
     *
     * @throws Exception
     */
    public function handle(): ?int
    {
        return $this->installBriesCmsStack();
    }

    /**
     * Execute the installation process.
     *
     * Steps:
     * 1. Installing Bries
     * 2. Installing CMS
     *
     * @return int Exit code (0: success, 1: failure)
     *
     * @throws Exception When any installation step fails
     */
    protected function installBriesCmsStack(): int
    {
        try {
            $this->components->info('Starting default installation...');

            $this->components->info('Installing Bries...');

            $this->call('bries:copy', [
                'dark' => 1,
                'grid' => 0,
                'cheatsheet' => 1,
                'pest' => 1,
                'backup' => 0,
            ]);

            $this->components->info('Installing CMS...');

            $this->call('cms:install', [
                'pest' => 1,
                'backup' => 0,
            ]);

            $this->components->success('Installation successful!');

            return 0;
        } catch (Exception $e) {
            $this->components->error("Installation failed: {$e->getMessage()}");

            return 1;
        }
    }
}
