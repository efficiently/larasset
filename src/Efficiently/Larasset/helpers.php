<?php

if (! function_exists('asset_path')) {

    /**
     * Computes the path to asset in public directory.
     *
     * @param string $source
     * @return string
     */
    function asset_path($source)
    {
        return Asset::assetPath($source);
    }
}

if (! function_exists('asset_url')) {

    /**
     * Computes the full URL to a asset in the public directory.
     * This will use Asset::assetPath() internally, so most of their behaviors will be the same.
     *
     * @param string $source
     * @return string
     */
    function asset_url($source)
    {
        return Asset::assetUrl($source);
    }
}

if (! function_exists('javascript_include_tag')) {

    /**
     * Returns an HTML script tag for the sources and attributes specified as arguments.
     *
     * @param  array|string  $sources default "application"
     * @param  array         $attributes HTML attributes
     * @return string
     */
    function javascript_include_tag($sources = "application", $attributes = [])
    {
        return Asset::javascriptIncludeTag($sources, $attributes);
    }
}

if (! function_exists('stylesheet_link_tag')) {

    /**
     * Returns a HTML stylesheet link tag for the sources and attributes specified as arguments.
     *
     * @param  array|string  $sources default "application"
     * @param  array         $attributes HTML attributes
     * @return string
     */
    function stylesheet_link_tag($sources = "application", $attributes = [])
    {
        return Asset::stylesheetLinkTag($sources, $attributes);
    }
}

if (! function_exists('favicon_link_tag')) {

    /**
     * Returns a HTML favicon link tag for the source and attributes specified as arguments.
     *
     * @param  string  $source Relative URL
     * @param  array   $attributes HTML attributes
     * @return string
     */
    function favicon_link_tag($source = null, $attributes = [])
    {
        return Asset::faviconLinkTag($source, $attributes);
    }
}

if (! function_exists('image_tag')) {

    /**
     * Returns an HTML image element for the source and attributes specified as arguments from the assets pipeline.
     *
     * @param  string  $source Relative URL
     * @param  string  $alt
     * @param  array   $attributes HTML attributes
     * @return string
     */
    function image_tag($source, $alt = null, $attributes = [])
    {
        return Asset::imageTag($source, $alt, $attributes);
    }
}
