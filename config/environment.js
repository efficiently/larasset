'use strict';


//
// Require some modules
//


var path      = require('path');
var Mincer    = require('mincer');

//
// Configure Mincers logger, by default, all
// messages are going to the middle of nowhere
//

Mincer.logger.use(console);

//
// Create and export environment
//


var environment = module.exports = new Mincer.Environment(__dirname);


var laravelRoot = environment.laravelRoot = process.env.LARAVEL_ROOT ||
  path.resolve((__dirname + '/../../../../').replace(/\\/g, '/')).replace(/\\/g, '/');


//
// Enable source maps support
//

environment.enable('source_maps');
//environment.sourceRoot = '/'; // use to cheat nesting level in dev tools

//
// Configure environment load paths (where to find assets)
//

(process.env.LARASSET_PATH || '').split('|').forEach(function (path) {
  if (path) {
    environment.appendPath(path.replace(/\\/g,"\/"));// Windows support
  }
});

// environment.appendPath(laravelRoot + '/app/assets/images');
// environment.appendPath(laravelRoot + '/app/assets/javascripts');
// environment.appendPath(laravelRoot + '/app/assets/stylesheets');
// environment.appendPath(laravelRoot + '/lib/assets/images');
// environment.appendPath(laravelRoot + '/lib/assets/javascripts');
// environment.appendPath(laravelRoot + '/lib/assets/stylesheets');
// environment.appendPath(laravelRoot + '/provider/assets/images');
// environment.appendPath(laravelRoot + '/provider/assets/javascripts');
// environment.appendPath(laravelRoot + '/provider/assets/stylesheets');

//
// Cache compiled assets.
//
// You want this to be enabled on your dev/staging/production environment.
// In order to enable it, uncomment following line. We keep it disabled in
// order to quick-test new featurees without bumping up Mincer's version.
//


// environment.cache = new Mincer.FileStore(path.join(__dirname, 'cache'));


//
// Define environment essential *_path helper that will be available in the
// processed assets. See `app/assets/stylesheets/application.css.ejs` for example.
//


environment.ContextClass.defineAssetPath(function (pathname, options) {
  var asset = this.environment.findAsset(pathname, options);

  if (!asset) {
    throw new Error("File " + pathname + " not found");
  }

  return (process.env.LARASSET_PREFIX || '/assets') + '/' + asset.digestPath;
});


environment.enable("autoprefixer");


//
// Prepare production-ready environment
//

process.env.NODE_ENV = process.env.LARASSET_ENV || 'development';

if (process.env.NODE_ENV === (process.env.ASSETS_ENV || 'production')) {

  //
  // Enable JS and CSS compression
  //
  // environment.disable('source_maps');

  environment.jsCompressor  = "uglify";
  // (!) use csswring, because csso does not supports sourcemaps
  environment.cssCompressor = "csswring";

  //
  // In production we assume that assets are not changed between requests,
  // so we use cached version of environment. See API docs for details.
  //

  environment = environment.index;

}
