<?php namespace Efficiently\Larasset\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use File;

class PrecompileAssetsCommand extends AssetsCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larasset:precompile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Precompiling Assets';

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

        $environment = $this->option('environment');
        $packagePath = $this->packagePath();

        $searchPaths = array_map(
            function($path) {
                return $this->normalizePath($path);
            },
            Config::get('larasset::paths', [])
        );
        putenv('LARASSET_PATH='.implode('|', $searchPaths));
        $precompileFiles = array_map(
            function($path) {
                return $this->normalizePath($path);
            },
            Config::get('larasset::precompile', [])
        );
        putenv('LARASSET_PRECOMPILE='.implode('|', $precompileFiles));
        putenv('LARASSET_ENV='.$environment);
        putenv('LARASSET_COMMAND=precompile');
        putenv('LARASSET_PREFIX='.Config::get('larasset::prefix'));
        $assetsPrecompileCommand = "larasset";

        // Precompile assets
        system("cd ".$packagePath." && ".$assetsPrecompileCommand);

        // $this->deleteManifest();
        // $this->copyAssets();
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
             ['environment', null, InputOption::VALUE_OPTIONAL, 'Specifies the environment to run this precompilation under.', 'development'],

        ];
    }

}
