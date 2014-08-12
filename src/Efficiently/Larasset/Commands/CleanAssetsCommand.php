<?php namespace Efficiently\Larasset\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use File;

class CleanAssetsCommand extends AssetsCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larasset:clean';

    /**
     * The console command description.
     *
     * Add a 'larasset:clean' command to only removes old assets (keeps the most recent 3 copies) from `public/assets`.
     * Useful when doing rolling deploys that may still be serving old assets while the new ones are being compiled.
     *
     * @var string
     */
    protected $description = 'Remove old assets (keeps the most recent 3 copies) from `public/assets`';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        parent::fire();

        $environment = "production";
        $gemFilePath = $this->normalizePath($this->option('gemfile-path'));
        $packagePath = $this->packagePath();
        if ($this->useWindows()) {
            putenv('BUNDLE_GEMFILE='.$gemFilePath);
            $bundleGemfile = 'setx BUNDLE_GEMFILE "'.$gemFilePath.'" > nul';
        } else {
            $bundleGemfile = 'BUNDLE_GEMFILE "'.$gemFilePath.'"';
        }
        $this->envs = array_add($this->envs, 'BUNDLE_GEMFILE', $gemFilePath);

        $assetsCleanCommand = $bundleGemfile." && ".$this->getRakeCommand()." assets:clean RAILS_ENV=".$environment;

        // Clean assets
        system("cd ".$packagePath." && ".$assetsCleanCommand);

        $destination = $this->normalizePath(public_path('assets'));

        // Delete old assets
        if (File::isDirectory($destination)) {
            $this->deleteTree($destination);
        }
        $this->copyAssets();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', 'default value'],
        ];
    }
}
