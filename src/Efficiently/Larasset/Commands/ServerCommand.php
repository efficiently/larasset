<?php namespace Efficiently\Larasset\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;

class ServerCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server and its asset files';

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
        $serverEnv = $this->option('env');
        $assetsServerHost = $this->option('larasset-host');
        $assetsServerPort = $this->option('larasset-port');
        putenv('LARASSET_PORT='.$assetsServerPort);
        if ($this->option('larasset-environment')) {
            // TODO: Remove the DEPRECATED stuff in the next minor version (0.10.0 or 1.0.0)
            $this->comment("WARN: The '--larasset-environment' option is DEPRECATED, use '--larasset-env' option instead please.");
            $assetsServerEnv = $this->option('larasset-environment');
        } else {
            $assetsServerEnv = $this->option('larasset-env');
        }

        // Run assets server in a background process
        $command = "php artisan larasset:serve --port=".$assetsServerPort." --host=".$assetsServerHost." --assets-env=".$assetsServerEnv;
        $this->info("Start the assets server...");

        $serverLogsPath = $this->normalizePath(storage_path('logs/larasset_server.log'));
        $this->line('Assets server logs are stored in "'.$serverLogsPath.'"');
        $this->execInBackground($command, $serverLogsPath);

        // Run PHP application server
        $this->call('serve', ['--host' => $serverHost, '--port' => $serverPort, '--env' => $serverEnv]);
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
            ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', "localhost"],
            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8000],
            ['larasset-host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the asset files on.', "localhost"],
            ['larasset-port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the asset files on.', config('larasset.port', 3000)],
            ['larasset-env', null, InputOption::VALUE_OPTIONAL, 'Specifies the assets environment to run this server under (test/development/production).', 'development'],
            ['larasset-environment', null, InputOption::VALUE_OPTIONAL, "DEPRECATED: Use '--larasset-env' option instead."],

        ];
    }

    protected function execInBackground($command, $output = null)
    {
        $output = $output ?: ($this->useWindows() ? 'nul': '/dev/null'); // no output by default
        if ($this->useWindows()) {
            // Source: http://arstechnica.com/civis/viewtopic.php?p=9058895&sid=ca678fb8e1cf654f5efae647716a343b#p9058895
            if (! class_exists("\COM")) {
                $this->error("Please install COM extension for PHP, see: http://www.php.net/manual/en/com.installation.php");
            }
            $WshShell = new \COM("WScript.Shell");
            $WshShell->Run("cmd /c title $command && ".$command." > ".$output." 2>&1 &", 2, false);
        } else {
            // For Linux and Mac OS platforms
            // TODO: Try pcntl_exec() function instead
            shell_exec(sprintf('%s > '.$output.' 2>&1 &', $command));
        }
    }
}
