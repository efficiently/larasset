Larasset
========

[![Build Status](https://travis-ci.org/efficiently/larasset.svg?branch=1.1)](https://travis-ci.org/efficiently/larasset)

The Asset Pipeline for **Laravel 5.1** !

The asset pipeline provides a framework to concatenate and minify or compress
JavaScript and CSS assets. It also adds the ability to write these assets in
other languages and pre-processors such as CoffeeScript, LESS, Sass and EJS.

For [Laravel 4.1 or 4.2](http://laravel.com/docs/4.2) supports see [Larasset 0.9 branch](https://github.com/efficiently/larasset/tree/0.9)

For [Laravel 5.0](http://laravel.com/docs/5.0) supports see [Larasset 1.0 branch](https://github.com/efficiently/larasset/tree/1.0)

For a more complete description of this package, you can read the Wiki docs:
* The [Asset Pipeline](https://github.com/efficiently/larasset/wiki/Asset-pipeline) Guide
* [Working with Ajax/JavaScript in Laravel](https://github.com/efficiently/larasset/wiki/Working-with-JavaScript-and-Larasset) Guide

Examples of Larasset usage.

- `php artisan larasset:precompile`: Precompile assets of your application, useful for your production environment
- `php artisan larasset:serve`:      Launch Larasset's server for serving assets, useful for your development environment
- `php artisan server`:              Serve your Laravel application on the PHP development server and also the Larasset's server for serving assets

Demo application
----------------

You can see this package in action with this [**online demo**](http://larasset.herokuapp.com/messages).
And you can grab the source code of this demo [here](https://github.com/efficiently/laravel_larasset_app/tree/bootstrap-l5.1).

Prerequisites
-------------

You must [install Node.js](http://nodejs.org) on your computer (development environment only).

This package is **only** compatible with **PHP >= 5.5** and **Laravel >= 5.1** framework.

Installation
------------

### Install and config Larasset package

1. In the `composer.json`, replace the line `"minimum-stability": "stable"` by:

    ```javascript
        "minimum-stability": "dev"
    ```

2. Install Larasset package with composer:

    ```sh
    composer require efficiently/larasset:1.1.*
    ```

3. Turn on your application debug mode, create or edit the `config/app.php` file:

    ```php
    <?php

    return [
        'debug' => true,
        // Others config options....
    ];
    ```

    Note: It is strongly recommended that you turn off error detail in your production environment.

4. Add these services providers to `config/app.php`:

    ```php
            'Collective\Html\HtmlServiceProvider',
            'Efficiently\Larasset\LarassetServiceProvider',
            'Efficiently\JqueryLaravel\JqueryLaravelServiceProvider',
    ```

5. Add these alias (facades) to your Laravel `config/app.php` file:

    ```php
           'Form' => 'Collective\Html\FormFacade',
           'HTML' => 'Collective\Html\HtmlFacade',
           'Asset' => 'Efficiently\Larasset\Facades\Asset',
    ```

6. You can now add the this security Middleware to your `app/Http/Kernel.php` file:

    ```php
            'Efficiently\JqueryLaravel\VerifyJavascriptResponse',
    ```

7. You will need install some [Node.js](http://nodejs.org/) modules in order to run these Larasset commands:

    ```sh
    npm install -g larasset-js
    ```

8. Finally run `php artisan larasset:setup`.

    The rest of the installation depends on whether the asset pipeline is being used.

Assets middleware server
------------------------

Run:

    php artisan larasset:serve

NOTICE: You should use it **only** in a development/local environment


Precompiling assets (Manifest usage)
------------------------------------

Run:

    php artisan larasset:precompile

NOTICE: You are encouraged to use it in a production environment,
for more informations, **read the next section**.


Development VS Production mode
------------------------------

By default Larasset is running in _development_ mode. That means that it will
recompile (server) any changed asset on demand. Also it's not compressing
JavaScript and/or Stylesheets in development mode. To run Larraset's server and
precompiler in production-ready mode, use `--assets-env production` command line
option, like so:

    php artisan larasset:precompile --assets-env production


Changelog
---------
### [1.1-dev](https://github.com/efficiently/larasset/tree/1.1)
 * Laravel 5.1 support!
 * [ECMAScript 2015](http://ecma-international.org/ecma-262/6.0) (ES6) support with [Babel](http://babeljs.io/) via [`larasset-js` 1.1.1](https://github.com/efficiently/larasset-js/tree/1.1.1)
 * Add the `humanize()` function helper to fix the `image_tag()` helper (fix #15).
 * Replace the deprecated package [`illuminate/html`](https://github.com/illuminate/html) package by the [`laravelcollective/html`](https://github.com/LaravelCollective/html) package
 * **Upgrade Notes** <small>(if you used previously this package with Laravel 4.x or 5.0)</small>:  
   * You should replace in the `config/app.php` file of your Laravel application:
     1. `'Illuminate\Html\HtmlServiceProvider',` by `'Collective\Html\HtmlServiceProvider',`
     2. `'Form' => 'Illuminate\Html\FormFacade',` by `'Form' => 'Collective\Html\FormFacade',`
     3. `'HTML' => 'Illuminate\Html\HtmlFacade',` by `'HTML' => 'Collective\Html\HtmlFacade',`

### [1.0.2](https://github.com/efficiently/larasset/tree/1.0.2)
  * Fix PHP 5.4 support.

### [1.0.1](https://github.com/efficiently/larasset/tree/1.0.1)
  * Add the `humanize()` function helper to fix the `image_tag()` helper (fix #15).

### [1.0.0](https://github.com/efficiently/larasset/tree/1.0.0)
 * Laravel 5.0 support!

### [0.9.8](https://github.com/efficiently/larasset/tree/0.9.8)
 * Add the `humanize()` function helper to fix the `image_tag()` helper (fix #15).

### [0.9.7](https://github.com/efficiently/larasset/tree/0.9.7)
 * Add an option to disable Source Mapping.

### [0.9.6](https://github.com/efficiently/larasset/tree/0.9.6)
  * **Breaking changes:**
  The `--environment` command line option is renamed to `--assets-env`. Because there was some conflicts with the Laravel command line option `--env`. And `larasset-environment` command line option is renamed to `--larasset-env`.
  See issue [#6](https://github.com/efficiently/larasset/issues/6) for more information.

Credits
-------

* The original [Asset Pipeline](https://github.com/rails/sprockets-rails) (Sprockets) from the Ruby on Rails framework.
* A Node.js port of the Asset Pipeline: [Mincer](https://github.com/nodeca/mincer)
* A  PHP port of the [Asset Pipeline](https://github.com/CodeSleeve/asset-pipeline)


Acknowledgements
----------------

Released under the MIT License.

#### This is beta-quality software
It works well according to our tests. The internal API may change and other features will be added.
We are working to make Larasset production quality software.
