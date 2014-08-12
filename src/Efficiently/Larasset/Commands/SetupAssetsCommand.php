<?php namespace Efficiently\Larasset\Commands;

use Illuminate\Console\Command as IlluminateCommand;

/**
 * Borrowed from:
 * https://github.com/CodeSleeve/asset-pipeline/blob/v2.0.3/src/Codesleeve/AssetPipeline/Commands/AssetsSetupCommand.php
 */
class SetupAssetsCommand extends IlluminateCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larasset:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Setup the default asset pipeline folders in your new Laravel project";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $structure = __DIR__ . '/../../../../structure';
        $base = base_path();

        $this->line('');
        $this->line('Creating initial directory structure and copying some general purpose assets over.');
        $this->line('');

        $this->xcopy(realpath($structure), realpath($base));

        $this->line('');
        $this->line('Finished.');
    }

    private function xcopy($source, $dest)
    {
        $base = base_path();
        foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                if (! is_dir($dest . '/' . $iterator->getSubPathName())) {
                    mkdir($dest . '/' . $iterator->getSubPathName());
                }
            } else {
                copy($item, $dest . '/' . $iterator->getSubPathName());
                $this->line('   Copying -> ' . str_replace($base, '', $dest . '/' . $iterator->getSubPathName()));
            }
        }
    }
}
