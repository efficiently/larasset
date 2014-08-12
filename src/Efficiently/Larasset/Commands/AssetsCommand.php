<?php namespace Efficiently\Larasset\Commands;

use File;

abstract class AssetsCommand extends BaseCommand
{
    /**
     * Copy all asset files in the current Laravel application 'public/assets' folder
     *
     * @return void
     */
    protected function copyAssets()
    {
        $packagePath = $this->packagePath();

        if ($this->useWindows()) {
            $source = $packagePath."/public/assets";
        } else {
            $source = $packagePath."/public/assets/";
        }
        $destination = $this->normalizePath(public_path('assets'));
        if (! File::exists($source)) {
            File::makeDirectory($source);
        }
        if (! File::exists($destination)) {
            File::makeDirectory($destination);
        }
        $copyAssetsCommand = $this->copyCommand().' '.$this->copyOptions().' "'.$source.'" "'.$destination.'" > nul';

        shell_exec($copyAssetsCommand);
    }

    /**
     * Returns the copy comment specific to your OS
     * @return string
     */
    protected function copyCommand()
    {
        $sys = strtoupper(PHP_OS);

        if (substr($sys, 0, 3) == "WIN") {
            $copyCommand = "xcopy";
        } elseif ($sys == "LINUX") {
            $copyCommand = "cp";
        } else {
            $copyCommand = "cp"; // MacOS
        }

        return $copyCommand;
    }

    protected function copyOptions()
    {
        $sys = strtoupper(PHP_OS);

        if (substr($sys, 0, 3) == "WIN") {
            // Copy all files recursively, verifies each new file and overwrites existing files without prompting you.
            $copyOptions = "/E /V /Y";
        } elseif ($sys == "LINUX") {
            // Preserve date creation attribute, copy all files recursively and treat destination as a normal file
            $copyOptions = "-pRT";
        } else {
            $copyOptions = "-pR"; // MacOS
        }

        return $copyOptions;
    }

    /**
     * Delete Manifest file(s) in the application's assets path
     *
     * @return void
     */
    protected function deleteManifest()
    {
        $manifests = find_paths(public_path('assets/').'manifest-*.json');
        foreach ($manifests as $manifest) {
            File::delete($manifest);
        }
    }

    /**
     * Delete recursively a tree of folders
     * Source: http://www.php.net/manual/fr/function.rmdir.php#110489
     *
     * @param string $dir Base directory
     * @return bool
     */
    protected function deleteTree($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }
}
