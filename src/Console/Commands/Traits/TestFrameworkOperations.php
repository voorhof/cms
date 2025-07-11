<?php

namespace Voorhof\Cms\Console\Commands\Traits;

use Exception;
use Illuminate\Filesystem\Filesystem;

/**
 * Test Unit Operations
 *
 * Provides methods for installing the preferred test unit.
 */
trait TestFrameworkOperations
{
    /**
     * Copy testsuite files based on the given argument.
     */
    protected function installTests(): bool
    {
        (new Filesystem)->ensureDirectoryExists(base_path('tests'));

        try {
            if ($this->argument('pest') || $this->isUsingPest()) {
                // Use trait methods for package management
                if ($this->hasComposerPackage('phpunit/phpunit')) {
                    if (! $this->manageComposerPackages(['phpunit/phpunit'], 'remove', true)) {
                        $this->error('Failed to remove PHPUnit');

                        return false;
                    }
                }

                if (! $this->manageComposerPackages(
                    ['pestphp/pest', 'pestphp/pest-plugin-laravel'],
                    'require',
                    true
                )) {
                    $this->error('Failed to install Pest');

                    return false;
                }

                (new Filesystem)->copyDirectory(
                    __DIR__.'/../../../../stubs/default/tests-pest/Feature/Cms',
                    base_path('tests/Feature/Cms')
                );
                copy(__DIR__.'/../../../../stubs/default/tests-pest/Pest.php', base_path('tests/Pest.php'));
            } else {
                (new Filesystem)->copyDirectory(
                    __DIR__.'/../../../../stubs/default/tests/Feature/Cms',
                    base_path('tests/Feature/Cms')
                );
            }

            return true;
        } catch (Exception $e) {
            $this->error("Test installation failed: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Determine whether the project is already using Pest.
     */
    protected function isUsingPest(): bool
    {
        return class_exists(\Pest\TestSuite::class);
    }
}
