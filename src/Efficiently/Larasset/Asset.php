<?php namespace Efficiently\Larasset;

use App;
use File;
use Request;
use URL;

class Asset
{
    const URI_REGEXP = '/^[-a-z]+:\/\/|^(?:cid|data):|^\/\//i';

    protected $args = [];
    protected $manifests = [];
    protected $assetPort;
    protected $assetsPrefix;
    protected $assetsHost;

    /**
     * Mutiple args $dir, $path, $options
     * @param mixed $args
     */
    public function __construct($args = null)
    {
        $this->args = is_array($args) ? $args : func_get_args();

        $this->assetsPrefix = function () {
            return config('larasset.prefix', '/assets');
        };

        $this->assetsHost = function () {
            return config('larasset.host');
        };

        $this->assetPort = function () {
            return getenv('LARASSET_PORT') ?: config('larasset.port', 3000);
        };
    }

    public function manifest($args = null)
    {
        $args = func_get_args() ? func_get_args() : $this->args;
        $manifestHash = md5(json_encode($args));
        if (array_get($this->manifests, $manifestHash)) {
            return $this->manifests[$manifestHash];
        }

        return $this->manifests[$manifestHash] = App::make('manifest', $args);
    }

    //  Borrow assets and files methods of Sprockets Manifest class and its Rails integration.
    //  In a Rails console we can access this methods via helper.assets_manifest.assets and helper.assets_manifest.files
    // TODO: Borrow these methods: https://github.com/CodeSleeve/asset-pipeline/blob/v1.3/src/Codesleeve/AssetPipeline/SprocketsTags.php#L132

    /**
     * Returns an HTML script tag for the sources and attributes specified as arguments.
     *
     * @param  array|string  $sources default "application"
     * @param  array   $attributes HTML attributes
     * @return string
     */
    public function javascriptIncludeTag($sources = "application", $attributes = [])
    {
        // E.g. javascript_include_tag('app'); => app-9fcd9b50e5cb047af35d3c5a4b55f73f.js
        $args = func_get_args();
        if (is_array(last($args))) {
            $attributes = array_pop($args);
        } else {
            $attributes = [];
        }
        $sources = is_array($sources) ? $sources : $args;
        if (empty($sources)) {
            $sources = ["application"];
        }

        $assetsOptions = ['host' => array_pull($attributes, 'host', $this->assetsHost)];

        $defaults = ['type' => 'text/javascript'];
        $attributes = $attributes + $defaults;

        $javascript_tags = [];
        foreach ((array) $sources as $source) {
            $sourceName = "$source.js";
            $javascript_tags[] = app('html')->script($this->assetPath($sourceName, $assetsOptions), $attributes);
        }

        return implode($javascript_tags);
    }

    /**
     * Returns a HTML stylesheet link tag for the sources and attributes specified as arguments.
     *
     * @param  array|string  $sources default "application"
     * @param  array   $attributes HTML attributes
     * @return string
     */
    public function stylesheetLinkTag($sources = "application", $attributes = [])
    {
        // E.g. stylesheet_link_tag('app', ['media'=>'all']); => app-fa2ce4b45369a106436f229ca9e52bee.css
        $args = func_get_args();
        if (is_array(last($args))) {
            $attributes = array_pop($args);
        } else {
            $attributes = [];
        }
        $sources = is_array($sources) ? $sources : $args;
        if (empty($sources)) {
            $sources = ["application"];
        }

        $assetsOptions = ['host' => array_pull($attributes, 'host', $this->assetsHost)];

        $defaults = ['media' => 'all', 'type' => 'text/css'];
        $attributes = $attributes + $defaults;

        $stylesheet_tags = [];
        foreach ((array) $sources as $source) {
            $sourceName = "$source.css";
            $stylesheet_tags[] = app('html')->style($this->assetPath($sourceName, $assetsOptions), $attributes);
        }

        return implode($stylesheet_tags);
    }

    /**
     * Returns a HTML favicon link tag for the source and attributes specified as arguments.
     *
     * @param  string  $source Relative URL
     * @param  array   $attributes HTML attributes
     * @return string
     */
    public function faviconLinkTag($source = null, $attributes = [])
    {
        // E.g. favicon_link_tag('favicon.ico', ['rel' => 'shortcut icon']); =>
        //      <link href='http://localhost/assets/favicon.ico' rel="shortcut icon" type="image/vnd.microsoft.icon">
        $source = $source ?: "favicon.ico";

        $assetsOptions = ['host' => array_pull($attributes, 'host', $this->assetsHost)];

        $defaults = ['rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon'];
        $attributes = $attributes + $defaults;

        return "<link href='".$this->assetPath($source, $assetsOptions)."'".app('html')->attributes($attributes).">".PHP_EOL;
    }

    /**
     * Returns an HTML image element for the source and attributes specified as arguments from the assets pipeline.
     *
     * @param  string  $source Relative URL
     * @param  string  $alt
     * @param  array   $attributes HTML attributes
     * @return string
     */
    public function imageTag($source, $alt = null, $attributes = [])
    {
        $assetsOptions = ['host' => array_pull($attributes, 'host', $this->assetsHost)];

        // E.g. image_tag('logo.png', "My Logo"); => <img src="http://localhost/assets/logo-f2331bb588d007ba354e5fa406f9f4aa.png" alt="My Logo">
        $alt = $alt ?: humanize(basename($source, ".".File::extension($source)));

        return app('html')->image($this->assetPath($source, $assetsOptions), $alt, $attributes);
    }

    /**
     * Computes the path to asset in public directory.
     *
     * @param string $source
     * @param array  $options
     * @return string
     */
    public function assetPath($source, array $options = [])
    {
        $source = (string) $source;
        if (! $source) {
            return ""; // Short circuit
        }

        if (preg_match(static::URI_REGEXP, $source)) {
            return $source;// Short circuit
        }

        $assetPrefix = array_get($options, 'prefix', $this->assetsPrefix);
        if (is_callable($assetPrefix) && is_object($assetPrefix)) {
            $assetPrefix = $assetPrefix();
        }

        $assetHost = array_get($options, 'host');
        if (is_callable($assetHost) && is_object($assetHost)) {
            $assetHost = $assetHost();
        }

        $assetPort = $this->assetPort;
        if (is_callable($assetPort) && is_object($assetPort)) {
            $assetPort = $assetPort();
        }

        $protocol = Request::secure() ? "https://" : "http://";
        if (App::environment() !== (getenv('ASSETS_ENV') ?: 'production')) {
            $assetHost = $assetHost ?: $protocol.$this->getHostname().":".$assetPort;
            $assetLocation = $assetHost.$assetPrefix;
        } else {
            $assetLocation = $assetHost.$assetPrefix;
            $manifest = static::manifest();
        }
        // TODO: Sanitize/purify $source var
        $sourceName = $source;
        if (isset($manifest) && $manifest->getAssets()) {
            $sourceName = array_get($manifest->getAssets(), $sourceName, $sourceName);
        }

        $assetPath = "$assetLocation/$sourceName";
        if (File::exists(public_path().$assetPath) || preg_match(static::URI_REGEXP, $assetPath)) {
            return $assetPath;
        } else {
            // TODO: The root path of the Laravel application is hardcoded here, it might be a problem
            return '/'.preg_replace('/^\//', '', $source);
        }
    }

    /**
     * Computes the full URL to a asset in the public directory.
     * This will use Asset::assetPath() internally, so most of their behaviors will be the same.
     *
     * @param string $source
     * @param array  $options
     * @return string
     */
    public function assetUrl($source, array $options = [])
    {
        $assetPath = $this->assetPath($source, $options);

        return URL::asset($assetPath, Request::secure());
    }

    protected function getHostname()
    {
        if (Request::server('SERVER_NAME') == "0.0.0.0") {
            return gethostname();
        } else {
            return Request::server('SERVER_NAME');
        }
    }
}
