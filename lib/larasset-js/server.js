'use strict';

//
// Require some modules
//

var fs      = require('fs');
var path    = require('path');
var connect = require('connect');
var Mincer  = require('mincer');
var ArgumentParser = require('argparse').ArgumentParser;
// var shellwords = require('shellwords').split;

//
// Get Mincer environment
//

var environment = require('../../config/environment');

var laravelRoot = environment.laravelRoot;

////////////////////////////////////////////////////////////////////////////////

var cli = new ArgumentParser({
  prog: 'server',
  addHelp: true
});


cli.addArgument(['--host'], {
  help: 'The host address to serve the asset files on.  (default: "localhost")',
  defaultValue: 'localhost'
});

cli.addArgument(['--port'], {
  help: 'The port to serve the asset files on. (default: 3000)',
  defaultValue: 3000
});

cli.addArgument(['--protocol'], {
  help: 'The protocol to serve the asset files on. (default: "http")',
  defaultValue: 'http'
});

////////////////////////////////////////////////////////////////////////////////

var args = cli.parseArgs();
var host = args.host; // default: "localhost"
var port = args.port; // default: 3000
var protocol = args.protocol; // default: "http" You can change it to "https"
var rootUrl = protocol + '://' + host + ':'+ port;

//
// Create connect application
//


var app = connect();

// CORS middleware
// Allow Cross Domain requests, useful for Firefox font-face URL. See: http://davidwalsh.name/cdn-fonts
// WARNING: DON'T use this function in production!
// Borrowed from: http://stackoverflow.com/a/20776861
var allowCrossDomain = function(req, res, next) {
    if (req.url.match(/^.+\.(eot|ttf|otf|woff)(\?.*)?$/)) {
      res.setHeader('Access-Control-Allow-Origin', '*');
    }
    next();
};

//
// Attach assets server
//


app
  .use(allowCrossDomain)
  .use((process.env.LARASSET_PREFIX || '/assets') + '/', Mincer.createServer(environment));


//
// Attach some dummy handler, that simply renders layout
//

app.use(function (req, res, next) {
  var data;

  if ("/" !== req.url) {
    res.statusCode = 404;
    res.end("Not Found");
    return;
  }

  res.end(data);
});


//
// Start listening
//

app.listen(port, host, function (err) {
  if (err) {
    console.error("Failed start server: " + (err.message || err.toString()));
    process.exit(128);
  }
  console.info('Listening on ' + rootUrl);
});
