<?php

namespace Voorhof\Cms\Console\Commands\Traits;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * File Operations Trait
 *
 * Manages file copy during installation.
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

        // // Requests
        $this->filesystem->ensureDirectoryExists(app_path('Http/Requests/Cms'));
        $this->filesystem->copyDirectory($this->stubPath.'/default/app/Http/Requests/Cms', app_path('Http/Requests/Cms'));

        // // Models
        $this->filesystem->ensureDirectoryExists(app_path('Models'));

        $userModel = app_path('Models/User.php');
        $userModelBackup = app_path('Models/User.php.backup-cms');
        if (! file_exists($userModelBackup) && $this->argument('backup')) {
            copy($userModel, $userModelBackup);
        }
        copy($this->stubPath.'/default/app/Models/User.php', $userModel);

        $postModel = app_path('Models/Post.php');
        $postModelBackup = app_path('Models/Post.php.backup-cms');
        if (! file_exists($postModelBackup) && $this->argument('backup')) {
            copy($postModel, $postModelBackup);
        }
        copy($this->stubPath.'/default/app/Models/Post.php', $postModel);

        // // Providers
        $this->filesystem->ensureDirectoryExists(app_path('Providers'));
        $provider = app_path('Providers/AppServiceProvider.php');
        $providerBackup = app_path('Providers/AppServiceProvider.php.backup-cms');
        if (! file_exists($providerBackup) && $this->argument('backup')) {
            copy($provider, $providerBackup);
        }
        copy($this->stubPath.'/default/app/Providers/AppServiceProvider.php', $provider);

        // // Components
        $this->filesystem->ensureDirectoryExists(app_path('View/Components'));
        copy($this->stubPath.'/default/app/View/Components/CmsLayout.php', app_path('View/Components/CmsLayout.php'));

        // Bootstrap
        $this->filesystem->ensureDirectoryExists(base_path('bootstrap'));

        $app = base_path('bootstrap/app.php');
        $appBackup = base_path('bootstrap/app.php.backup-cms');
        if (! file_exists($appBackup) && $this->argument('backup')) {
            copy($app, $appBackup);
        }
        copy($this->stubPath.'/default/bootstrap/app.php', $app);

        // Config
        $this->filesystem->ensureDirectoryExists(base_path('config'));
        copy($this->stubPath.'/default/config/cms.php', base_path('config/cms.php'));

        // Database
        $this->filesystem->ensureDirectoryExists(base_path('database'));
        if ($this->argument('backup')) {
            $this->filesystem->copyDirectory(base_path('database'), base_path('database.backup-cms'));
        }
        $this->filesystem->copyDirectory($this->stubPath.'/default/database', base_path('database'));

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
        if ($this->argument('backup')) {
            $this->filesystem->copyDirectory(base_path('routes'), base_path('routes.backup-cms'));
        }
        copy($this->stubPath.'/default/routes/cms.php', base_path('routes/cms.php'));
        $this->appendToWebRoutes();

        // Vite
        if ($this->argument('backup')) {
            $this->filesystem->copy(base_path('vite.config.js'), base_path('vite.config.js.backup-cms'));
            $this->filesystem->copy(base_path('package.json'), base_path('package.json.backup-cms'));
        }

        $this->replaceInFile(
            "'resources/js/app.js'",
            "'resources/js/app.js', 'resources/js/cms.js'",
            base_path('vite.config.js'));

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

    /**
     * Add the CMS routes to the web.php file
     */
    public function appendToWebRoutes(): void
    {
        $webRoutesPath = base_path('routes/web.php');

        if (! file_exists($webRoutesPath)) {
            throw new RuntimeException('routes/web.php not found.');
        }

        // Create backup
        if ($this->argument('backup')) {
            $backupPath = base_path('routes/web.php.backup-cms');
            if (! file_exists($backupPath)) {
                copy($webRoutesPath, $backupPath);
            }
        }

        $content = file_get_contents($webRoutesPath);

        // Check if the line already exists
        if (str_contains($content, "require __DIR__.'/cms.php'")) {
            return;
        }

        // Add a newline if the file doesn't end with one
        if (! str_ends_with($content, "\n")) {
            $content .= "\n";
        }

        // Add an extra newline for separation and then the new require statement
        $content .= "\n"."require __DIR__.'/cms.php';"."\n";

        if (! file_put_contents($webRoutesPath, $content)) {
            throw new RuntimeException('Error adding CMS routes to web.php');
        }
    }
}
