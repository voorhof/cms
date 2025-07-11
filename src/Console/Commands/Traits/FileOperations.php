<?php

namespace Voorhof\Cms\Console\Commands\Traits;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * File Operations Trait
 *
 * Manages file copy during Voorhof installation.
 *
 * @property-read OutputInterface $output
 *
 * @method void error(string $message)
 * @method void info(string $message)
 */
trait FileOperations
{
    private Filesystem $filesystem;

    private string $stubPath = __DIR__.'/../../../../stubs';

    protected function initializeFileSystem(): void
    {
        $this->filesystem = new Filesystem;
    }

    /**
     * Copy starter kit files to their respective locations.
     *
     * @return bool Success status
     */
    protected function copyFiles(): bool
    {
        $this->initializeFileSystem();

        // App
        // // Controllers
        $this->filesystem->ensureDirectoryExists(app_path('Http/Controllers/Cms'));
        $this->filesystem->copyDirectory($this->stubPath.'/default/app/Http/Controllers/Cms', app_path('Http/Controllers/Cms'));

        // // Components
        $this->filesystem->ensureDirectoryExists(app_path('View/Components'));
        copy($this->stubPath.'/default/app/View/Components/CmsLayout.php', app_path('View/Components/CmsLayout.php'));

        // // Config
        $this->filesystem->ensureDirectoryExists(base_path('config'));
        copy($this->stubPath.'/default/config/cms.php', base_path('config/cms.php'));

        // Resources
        // // JS
        $this->filesystem->ensureDirectoryExists(resource_path('js'));
        copy($this->stubPath.'/default/resources/js/cms.js', resource_path('js/cms.js'));

        // // SCSS
        $this->filesystem->ensureDirectoryExists(resource_path('scss'));
        copy($this->stubPath.'/default/resources/scss/cms.scss', resource_path('scss/cms.scss'));
        copy($this->stubPath.'/default/resources/scss/cms-bootstrap.scss', resource_path('scss/cms-bootstrap.scss'));
        copy($this->stubPath.'/default/resources/scss/cms-layout.scss', resource_path('scss/cms-layout.scss'));

        // // Views
        $this->filesystem->ensureDirectoryExists(resource_path('views/cms'));
        $this->filesystem->copyDirectory($this->stubPath.'/default/resources/views/cms', resource_path('views/cms'));
        $this->filesystem->ensureDirectoryExists(resource_path('views/components/cms'));
        $this->filesystem->copyDirectory($this->stubPath.'/default/resources/views/components/cms', resource_path('views/components/cms'));
        $this->filesystem->ensureDirectoryExists(resource_path('views/layouts/cms'));
        $this->filesystem->copyDirectory($this->stubPath.'/default/resources/views/layouts/cms', resource_path('views/layouts/cms'));
        copy($this->stubPath.'/default/resources/views/layouts/cms.blade.php', resource_path('views/layouts/cms.blade.php'));

        // Routes
        $this->filesystem->ensureDirectoryExists(base_path('routes'));
        copy($this->stubPath.'/default/routes/cms.php', base_path('routes/cms.php'));

        return true;
    }

    /**
     * Replace a given string within a file.
     *
     * @param  string  $search  Search string
     * @param  string  $replace  Replacement string
     * @param  string  $path  File path
     */
    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
