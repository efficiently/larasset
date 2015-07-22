<?php namespace Efficiently\Larasset\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ServeAssetsCommand extends AssetsCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larasset:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Serve the application's assets";

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

        $serverHost = $this->option('host');
        $serverPort = $this->option('port');
        if ($this->option('environment')) {
            # TODO: Remove the DEPRECATED stuff in the next minor version (0.10.0 or 1.0.0)
            $this->comment("WARN: The '--environment' option is DEPRECATED, use '--assets-env' option instead please.");
            $serverEnv = $this->option('environment');
        } else {
            $serverEnv = $this->option('assets-env');
        }

        $serverOptions = "--port=".$serverPort." --host=".$serverHost;
        $packagePath = $this->packagePath();

        $searchPaths = array_map(
            function ($path) {
                return $this->normalizePath($path);
            },
            config('larasset.paths', [])
        );
        putenv('LARASSET_PATH='.implode('|', $searchPaths));
        putenv('LARASSET_PREFIX='.config('larasset.prefix'));
        putenv('LARASSET_ENV='.$serverEnv);
        putenv('LARASSET_COMMAND=server');
        $assetsServerCommand = "larasset ".$serverOptions;

        // Serve assets
        system("cd ".$packagePath." && ".$assetsServerCommand);
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
            ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the asset files on.', "localhost"],
            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the asset files on.', config('larasset.port', 3000)],
            ['assets-env', null, InputOption::VALUE_OPTIONAL, 'Specifies the assets environment to run this server under (test/development/production).', 'development'],
            ['environment', null, InputOption::VALUE_OPTIONAL, "DEPRECATED: Use '--assets-env' option instead."],
        ];
    }
}
