<?php namespace Efficiently\Larasset\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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

        if ($this->option('environment')) {
            # TODO: Remove the DEPRECATED stuff in the next minor version (0.10.0 or 1.0.0)
            $this->comment("WARN: The '--environment' option is DEPRECATED, use '--assets-env' option instead please.");
            $assetsEnv = $this->option('environment');
        } else {
            $assetsEnv = $this->option('assets-env');
        }

        $packagePath = $this->packagePath();

        $searchPaths = array_map(
            function ($path) {
                return $this->normalizePath($path);
            },
            config('larasset.paths', [])
        );
        putenv('LARASSET_PATH='.implode('|', $searchPaths));
        $precompileFiles = array_map(
            function ($path) {
                return $this->normalizePath($path);
            },
            config('larasset.precompile', [])
        );
        putenv('LARASSET_PRECOMPILE='.implode('|', $precompileFiles));
        putenv('LARASSET_ENV='.$assetsEnv);
        putenv('LARASSET_COMMAND=precompile');
        putenv('LARASSET_PREFIX='.config('larasset.prefix'));
        $enableSourceMaps = config('larasset.sourceMaps') === null ? true : config('larasset.sourceMaps');
        putenv('LARASSET_SOURCE_MAPS='.($enableSourceMaps ? 'true' : 'false'));
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
            ['assets-env', null, InputOption::VALUE_OPTIONAL, 'Specifies the assets environment to run this precompilation under.', 'development'],
            ['environment', null, InputOption::VALUE_OPTIONAL, "DEPRECATED: Use '--assets-env' option instead."],

        ];
    }
}
