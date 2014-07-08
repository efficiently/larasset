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
         * Search Paths
         *
         * When a file is referenced from a manifest or a helper,
         * Larasset searches the three default asset locations for it.
         *
         * Besides the standard assets/* paths, additional (fully qualified)
         * paths can be added to the pipeline
         */
        'paths' => array_merge(
            // Including assets files in `app/assets` folders of your Laravel packages.
            File::glob(base_path().'/vendor/*/*/app/assets/*/'), [
            base_path().'/app/assets/images',
            base_path().'/app/assets/javascripts',
            base_path().'/app/assets/stylesheets',
            base_path().'/lib/assets/images',
            base_path().'/lib/assets/javascripts',
            base_path().'/lib/assets/stylesheets',
            base_path().'/vendor/assets/images',
            base_path().'/vendor/assets/javascripts',
            base_path().'/vendor/assets/stylesheets',
            // base_path().'/vendor/videoplayer/flash',
        ]),

        /**
         * Precompile files
         *
         * The default matcher for compiling files includes application.js, application.css
         * and all non-JS/CSS files (this will include all image assets automatically)
         * from app/assets folders including your Laravel packages.
         *
         * If you have other manifests or individual stylesheets and JavaScript files
         * to include, you can add them to this precompile array.
         */
        'precompile' => [
            'application.css',
            'application.js',
            // 'admin.js',
            // 'admin.css',
        ],

];

