'use strict';

//
// Require some modules
//

var fs     = require('fs');
var path   = require('path');
var glob   = require('glob');
var Mincer = require('mincer');

//
// Get Mincer environment
//

var environment = require(__dirname + '/../../config/environment');

var laravelRoot = environment.laravelRoot;

//
// Create and compile Manifest
//

var manifest = new Mincer.Manifest(environment, laravelRoot + '/public/assets');


// TODO: Remove all `environment.paths` prefix in the relative paths of `nonJsCssPattern` var
//       instead of using hardcoded paths below '{app,{vendor,workbench}/*/*/app}/assets/**/*.*'
var nonJsCssPattern = laravelRoot + "/{app,{vendor,workbench}/*/*/app}/assets/**/*.*";

glob(nonJsCssPattern, function(err, defaultAssetPaths) {
  if (err) {
    console.error("Failed to find non-JS/CSS files: " + (err.message || err.toString()));
  }
  try {
      // The default matcher for compiling files includes application.js, application.css
      //  and all non-JS/CSS files (this will include all image assets automatically)
      //  from 'app/assets' folders including your Laravel packages:
      // TODO: DRY !!!
      var defaultAssets = defaultAssetPaths
          .map(function(assetPath) {
            // Convert current path to the relative path in the 'public/assets' of your Laravel application
            // E.G. '/var/www/my_laravel_app/app/assets/javascripts/backoffice/admin.js' path becomes 'backoffice/admin.js'
            var assetPathPattern = RegExp('^(' + laravelRoot + '/|)(app|(vendor|workbench)/[^/]+/[^/]+/app)/assets/[^/]+/(.+)$', 'i'); // Insensitive for Windows
            return assetPath.replace(assetPathPattern, '$4');
          }).filter(function(assetPath) {
            // Reject all JS/CSS files of 'app/assets' folders including your Laravel packages
            var assetExt = path.extname(assetPath);
            return environment.extensions.indexOf(assetExt) === -1;
          }).concat(process.env.LARASSET_PRECOMPILE && process.env.LARASSET_PRECOMPILE.split('|'))
          .map(function(assetPath) {
            // Convert current path to the relative path in the 'public/assets' of your Laravel application
            // E.G. '/var/www/my_laravel_app/app/assets/javascripts/backoffice/admin.js' path becomes 'backoffice/admin.js'
            var assetPathPattern = RegExp('^(' + laravelRoot + '/|)(app|lib|provider|vendor|(vendor|workbench)/[^/]+/[^/]+/(app|lib|provider|vendor))/assets/[^/]+/(.+)$', 'i'); // Insensitive for Windows
            return assetPath.replace(assetPathPattern, '$5');
          });

    var assetsData = manifest.compile(defaultAssets, {
                                        compress: true, // Slowed pre-compilation, if your server compress your assets on the fly set it to false
                                        sourceMaps: true, // NOTICE: Source-maps files shouldn't be published on a public web server
                                        embedMappingComments: true
                                      });


    var manifestDigest = environment.getFileDigest(manifest.path);
    var manifestDirName = path.dirname(manifest.path);
    var manifestExt = path.extname(manifest.path);
    var manifestBaseName = path.basename(manifest.path, manifestExt);
    var manifestDigestPath = (manifestDirName + path.sep + manifestBaseName + "-" + manifestDigest + manifestExt);

    var globManifestFiles = (manifestDirName + path.sep + manifestBaseName + "-*" + manifestExt);
    glob(globManifestFiles, function(err, files) {
      if (err) {
        console.error("Failed to find old manifest files: " + (err.message || err.toString()));
      }

      // Remove old manifest files:
      files.forEach(function(file) {
        fs.unlink(file);
      });

      // Rename 'manifest.json' with its hash value; E.g. 'manifest-ab161bb9c237a51587d5c4f7743346dd.json'
      fs.renameSync(manifest.path, manifestDigestPath);
    });

    console.info('\n\nAssets were successfully compiled.\n' +
                 'Manifest data (a proper JSON) was written to:\n' +
                 manifestDigestPath + '\n\n');

    console.dir(assetsData);
  } catch (err) {
    console.error("Failed compile assets: " + (err.message || err.toString()));
  }
});
