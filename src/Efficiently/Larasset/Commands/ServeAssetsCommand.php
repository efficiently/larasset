<?php namespace Efficiently\Larasset\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;
use File;

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
        $serverEnvironment = $this->option('environment');

        $serverOptions = "--port=".$serverPort." --host=".$serverHost;
        $packagePath = $this->packagePath();

        $searchPaths = array_map(
            function ($path) {
                return $this->normalizePath($path);
            },
            Config::get('larasset::paths', [])
        );
        putenv('LARASSET_PATH='.implode('|', $searchPaths));
        putenv('LARASSET_PREFIX='.Config::get('larasset::prefix'));
        putenv('LARASSET_ENV='.$serverEnvironment);
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
            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the asset files on.', 3000],
            ['environment', null, InputOption::VALUE_OPTIONAL, 'Specifies the environment to run this server under (test/development/production).', 'development'],
        ];
    }
}
