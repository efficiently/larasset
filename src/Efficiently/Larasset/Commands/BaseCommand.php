<?php namespace Efficiently\Larasset\Commands;

use Illuminate\Console\Command as IlluminateCommand;

abstract class BaseCommand extends IlluminateCommand
{

    protected $envs = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // Check prerequites
        if (! $this->hasNode()) {
            $this->error('Please install Node.');
            exit();
        }

        if (! $this->hasNpm()) {
            $this->error("Please install Npm.");
            exit();
        }
        if (! $this->hasLarassetJs()) {
            $this->error("Please install the Node.js module 'larasset-js'. Run in a terminal: 'npm install -g larasset-js'");
            exit();
        }

        putenv('LARAVEL_ROOT='.$this->normalizePath(base_path()));

    }

    /**
     * Windows platform support: Convert backslash to slash
     *
     * @param  string $path
     * @return string
     */
    protected function normalizePath($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Check if the PHP server is under Windows Operating System
     *
     * @return bool
     */
    protected function useWindows()
    {
        $sys = strtoupper(PHP_OS);

        return (substr($sys, 0, 3) == "WIN");
    }

    /**
     * Returns package absolute path
     *
     * @return string
     */
    protected function packagePath()
    {
        return $this->normalizePath(realpath(__DIR__."/../../../.."));
    }

    /**
     * Does user have Node.js installed?
     *
     * @return boolean
     */
    protected function hasNode()
    {
        if ($this->useWindows()) {
            $node = shell_exec('where node');
        } else {
            $node = shell_exec('which node');
        }

        return str_contains($node, 'node');
    }

    /**
     * Does user have Npm installed?
     *
     * @return boolean
     */
    protected function hasNpm()
    {
        if ($this->useWindows()) {
            $npm = shell_exec('where npm');
        } else {
            $npm = shell_exec('which npm');
        }

        return str_contains($npm, 'npm');
    }

    /**
     * Does user have larasset-js module installed?
     *
     * @return boolean
     */
    protected function hasLarassetJs()
    {
        if ($this->useWindows()) {
            $larasset = shell_exec('where larasset');
        } else {
            $larasset = shell_exec('which larasset');
        }

        return str_contains($larasset, 'larasset');
    }
}
