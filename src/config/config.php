<?php

return [

        /**
         * Changing the assets Path
         *
         * The public path that Larasset uses by default is /assets.
         *
         * This is a handy option if you are updating an older project
         * that didn't use the asset pipeline and already uses
         * this path or you wish to use this path for a new resource.
         */
        'prefix' => '/assets',

        /**
         * By default, Larasset links to these assets on the current host
         * in the public folder, but you can direct Larasset to link to assets
         * from a dedicated asset server by setting `larasset.host`
         * in the package configuration, typically in
         * `config/larasset.php`.
         * For example, you'd define `http://assets.example.com` to be your asset host
         * this way:
         *
         *     Config::set('larasset.host', 'http://assets.example.com');
         *
         * Helpers take that into account:
         *
         *     image_tag("logo.png");
         *     // -> <img src="http://assets.example.com/assets/logo.png" alt="Logo">
         *     stylesheet_link_tag("app");
         *     // -> <link media="all" type="text/css" rel="stylesheet" href="http://assets.example.com/assets/app.css">
         *
         * sets the host for the assets. Useful when CDNs are used for hosting
         * assets, or when you want to work around the concurrency constraints builtin in browsers
         * using different domain aliases.
         */
        'host' => null,

        /**
         * The port to serve the asset files on.
         * Useful for your development environment when you run the Assets server
         */
        'port' => 3000,

        /**
         * Search Paths
         *
         * When a file is referenced from a manifest or a helper,
         * Larasset searches the three default asset locations for it.
         *
         * Besides the standard assets/* paths, additional (fully qualified)
         * paths can be added to the pipeline
         */
        'paths' => array_merge(
            // Including assets files in `resources/assets` folders of your Laravel packages.
            find_paths(base_path().'/vendor/*/*/resources/assets/*/'),
            [
                base_path().'/resources/assets/images',
                base_path().'/resources/assets/js',
                base_path().'/resources/assets/css',
                base_path().'/lib/assets/images',
                base_path().'/lib/assets/js',
                base_path().'/lib/assets/css',
                base_path().'/provider/assets/images',
                base_path().'/provider/assets/js',
                base_path().'/provider/assets/css',
                // base_path().'/provider/videoplayer/flash',
            ]
        ),

        /**
         * Precompile files
         *
         * The default matcher for compiling files includes app.js, app.css
         * and all non-JS/CSS files (this will include all image assets automatically)
         * from resources/assets folders including your Laravel packages.
         *
         * If you have other manifests or individual stylesheets and JavaScript files
         * to include, you can add them to this precompile array.
         */
        'precompile' => [
            'app.css',
            'app.js',
            // 'admin.js',
            // 'admin.css',
        ],

        /**
         * Enable or disable Source Mapping.
         * Useful in development environment to debug precompiled files.
         * Source-maps files shouldn't be published on a public web server.
         * So you can disable them for your production environment.
         */
        'sourceMaps' => true,

];
