Larasset
========

The Asset Pipeline for Laravel >= 4.1 !

The asset pipeline provides a framework to concatenate and minify or compress
JavaScript and CSS assets. It also adds the ability to write these assets in
other languages and pre-processors such as CoffeeScript, LESS and EJS.

For more a complete description of this package, you can read the Wiki doc *[here](https://github.com/efficiently/larasset/wiki/Asset-pipeline)*.

Examples of Larasset usage.

- `php artisan larasset:precompile`: Precompile assets of your application, useful in your production environment
- `php artisan larasset:serve`:      Launch Larasset's server for serving assets, useful in your development environment
- `php artisan server`:              Serve your Laravel application on the PHP development server and also the Larasset's server for serving assets


Prerequisites
-------------

You must [install Node](http://nodejs.org) on your computer (development environment).
This package is **only** compatible with **PHP >= 5.4** and **Laravel >= 4.1** framework.

Installation
------------

1. In the `.gitignore` file of your Laravel application, replace the line `/vendor` by :

    ```
    /vendor/*
    !/vendor
    !/vendor/assets/
    ```

2. Then install Larasset package

    ```sh
    composer require efficiently/larasset:dev-master
    ```

    1. Add these two services providers to `app/config/app.php`:

        ```php
            'Efficiently\Larasset\LarassetServiceProvider',
            'Efficiently\JqueryLaravel\JqueryLaravelServiceProvider',
        ```

    2. Add the alias (facade) to your Laravel app config file:

        ```php
           'Asset' => 'Efficiently\Larasset\Facades\Asset',
        ```

    3. You will need install some [Node.js](http://nodejs.org/) modules in order to run these Larasset commands:

        ```sh
        npm install -g vendor/efficiently/larasset
        ```

    4. Then run `php artisan larasset:setup`. The rest of the installation depends on
    whether the asset pipeline is being used.

Assets middleware server
------------------------

Run:

    php artisan larasset:serve

NOTICE: You should use it *only* in a development environment


Precompiling assets (Manifest usage)
------------------------------------

Run:

    php artisan larasset:precompile

NOTICE: You are encouraged to use it in a production environment, 
for more informations, read the next section.


Development VS Production mode
------------------------------

By default Larasset is running in _development_ mode. That means that it will
recompile (server) any changed asset on demand. Also it's not compressing
JavaScript and/or Stylesheets in development mode. To run Larraset's server and
precompiler in production-ready mode, use `--environment production` command line
option, like so:

    php artisan larasset:precompile --environment production


Acknowledgements
----------------

Released under the MIT License.

#### This is alpha-quality software
It works well according to our applications. The internal API may change and other features will be added.
We are working to make AuthorityController production quality software.