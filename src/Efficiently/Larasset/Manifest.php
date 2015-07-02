<?php namespace Efficiently\Larasset;

use File;

// The Manifest logs the contents of assets compiled to a single
// directory. It records basic attributes about the asset for fast
// lookup without having to compile. A pointer from each logical path
// indicates which fingerprinted asset is the current one.
//
// The JSON is part of the public API and should be considered
// stable. This should make it easy to read from other programming
// languages and processes that don't have sprockets loaded. See
// `getAssets()` and `getFiles()` for more infomation about the structure.
class Manifest
{
    public $path;
    public $dir;
    protected $data = [];

    // Create new Manifest associated with an `path` is
    // a full path to the manifest json file. The file may or may not
    // already exist. The dirname of the `path` will be used to write
    // compiled assets to. Otherwise, if the path is a directory, the
    // filename will default a random "manifest-123.json" file in that
    // directory.
    //
    //   new \Efficiently\Larasset\Manifest("/public/assets/manifest.json");
    //
    public function __construct($args = null)
    {
        $args = is_array($args) ? $args : func_get_args();
        if (count($args) < 2) {
            $args = array_merge($args, [null]);
        }
        list($this->dir, $this->path) = $args;
        $this->dir = $this->normalizePath($this->dir);
        $this->path = $this->normalizePath($this->path);

        $basePath = $this->normalizePath(base_path()); // Windows support: Convert backslash to slash

        // Expand paths
        if ($this->dir) {
            $this->dir = $this->normalizePath(realpath($this->dir));
        }

        if ($this->path) {
            $this->path = $this->normalizePath(realpath($this->path));
        }

        // If path is given as the second arg
        if ($this->dir && File::extension($this->dir) != "") {
            list($this->dir, $this->path) = [null, $this->dir];
        }

        // Default dir to the directory of the path
        if ($this->path) {
            $this->dir = $this->dir ?: dirname($this->path);
        }

        // If directory is given w/o path, pick a random manifest.json location
        if ($this->dir && !$this->path) {
            // Find the first manifest.json in the directory
            $paths = find_paths($this->dir."/manifest*.json");

            if (! empty($paths)) {
                $this->path = head($paths);
            } else {
                $this->path = $this->dir."/manifest-".md5(uniqid(mt_rand(), true)).".json";
            }
        }

        if (! $this->dir && ! $this->path) {
            throw new Exception("manifest requires output path", 1);
        }

        $data = [];

        try {
            if (File::exists($this->path)) {
                // \Log::info("Load manifest !");//debug
                $data = json_decode(File::get($this->path), true);
            }
        } catch (Exception $e) {
            \Log::error($this->path." is invalid: ".get_classname($e)." ".$e->getMessage());
        }
        $this->data = $data;
    }

    // Returns internal assets mapping. Keys are logical paths which
    // map to the latest fingerprinted filename.
    //
    //   Logical path (String): Fingerprint path (String)
    //
    //   [ "app.js" => "app-2e8e9a7c6b0aafa0c9bdeec90ea30213.js",
    //     "jquery.js"      => "jquery-ae0908555a245f8266f77df5a8edca2e.js" ]
    //
    public function getAssets()
    {
        return array_get($this->data, 'assets', []);
    }

    // Returns internal file directory listing. Keys are filenames
    // which map to an attributes array.
    //
    //   Fingerprint path (String):
    //     logical_path: Logical path (String)
    //     mtime: ISO8601 mtime (String)
    //     digest: Base64 hex digest (String)
    //
    //  [ "app-2e8e9a7c6b0aafa0c9bdeec90ea30213.js" =>
    //      [ 'logical_path' => "app.js",
    //        'mtime' => "2011-12-13T21:47:08-06:00",
    //        'digest' => "2e8e9a7c6b0aafa0c9bdeec90ea30213" ] ]
    //
    public function getFiles()
    {
        return array_get($this->data, 'files', []);
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
}
