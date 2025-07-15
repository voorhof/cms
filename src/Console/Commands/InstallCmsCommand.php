<?php

namespace Voorhof\Cms\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Voorhof\Cms\Console\Commands\Traits\ComposerOperations;
use Voorhof\Cms\Console\Commands\Traits\DatabaseOperations;
use Voorhof\Cms\Console\Commands\Traits\FileOperations;
use Voorhof\Cms\Console\Commands\Traits\NodePackageOperations;
use Voorhof\Cms\Console\Commands\Traits\TestFrameworkOperations;

use function Laravel\Prompts\select;

/**
 * CMS Installation Command
 *
 * @property string $signature Command signature with arguments and options
 * @property string $description Command description
 */
#[AsCommand(name: 'cms:install')]
class InstallCmsCommand extends Command implements PromptsForMissingInput
{
    use ComposerOperations,
        DatabaseOperations,
        FileOperations,
        NodePackageOperations,
        TestFrameworkOperations;

    private const YES_NO_OPTIONS = [
        1 => 'Yes',
        0 => 'No',
    ];

    private const TESTING_FRAMEWORK_OPTIONS = [
        1 => 'Pest',
        0 => 'PHPUnit',
    ];

    private const NODE_DEPENDENCIES = [
        '@popperjs/core' => '^2.11.8',
        'autoprefixer' => '^10.4.21',
        'axios' => '^1.8.2',
        'bootstrap' => '^5.3.7',
        'bootstrap-icons' => '^1.13.1',
        'sass' => '^1.89.2',
    ];

    private const INSTALLATION_STEPS = [
        ['message' => 'Copying cms files...', 'method' => 'copyFiles'],
        ['message' => 'Setting up testunit...', 'method' => 'installTests'],
        ['message' => 'Updating node packages...', 'method' => 'updateNodeDependencies'],
        ['message' => 'Compiling node packages...', 'method' => 'compileNodePackages'],
        ['message' => 'Migrating database...', 'method' => 'migrateFreshSeed'],
    ];

    private const INSTALLATION_PROMPTS = [
        'pest' => [
            'label' => 'Which testing framework do you prefer?',
            'options' => self::TESTING_FRAMEWORK_OPTIONS,
            'default' => 1,
        ],
        'backup' => [
            'label' => 'Would you like to backup the original files?',
            'options' => self::YES_NO_OPTIONS,
            'default' => 0,
        ],
    ];

    /**
     * The command signature with available arguments and options.
     *
     * @var string
     *
     * Arguments:
     *   - pest: Use Pest as the testing framework
     *   - backup: Back up the original Laravel files
     *
     * Options:
     *   - composer: Path to Composer binary
     */
    protected $signature = 'cms:install
                                {pest : Indicate that Pest should be installed}
                                {backup : Indicate that original files should have a backup}
                                {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Install the CMS.';

    /**
     * Install the CMS components.
     *
     * @return int|null 0 on success, 1 on failure
     *
     * @throws Exception
     */
    public function handle(): ?int
    {
        return $this->installsCmsStack();
    }

    /**
     * Prompt for user input arguments.
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return array_map(
            fn (array $prompt) => fn () => select(
                label: $prompt['label'],
                options: $prompt['options'],
                default: $prompt['default'],
            ),
            self::INSTALLATION_PROMPTS
        );
    }

    /**
     * Execute the CMS installation process.
     *
     * Steps:
     * 1. Copy CMS files
     * 2. Set up the test framework
     * 3. Update Node.js dependencies
     * 4. Compile assets
     * 5. Database migration and seeding
     *
     * @return int Exit code (0: success, 1: failure)
     *
     * @throws Exception When any installation step fails
     */
    protected function installsCmsStack(): int
    {
        try {
            $this->components->info('Starting CMS installation...');

            foreach (self::INSTALLATION_STEPS as $index => $step) {
                $this->components->info(sprintf(
                    '(step %d/%d) %s',
                    $index + 1,
                    count(self::INSTALLATION_STEPS),
                    $step['message']
                ));

                if (! $this->{$step['method']}()) {
                    return 1;
                }
            }

            $this->components->success('CMS installation successful!');

            return 0;
        } catch (Exception $e) {
            $this->components->error("CMS installation failed: {$e->getMessage()}");

            return 1;
        }
    }

    /**
     * Update node dependencies
     */
    protected function updateNodeDependencies(): bool
    {
        $this->updateNodePackages(fn () => self::NODE_DEPENDENCIES);

        return true;
    }
}
