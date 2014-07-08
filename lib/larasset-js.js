'use strict';

if (process.env.LARASSET_COMMAND === 'precompile') {
   require(__dirname + '/larasset-js/precompile.js');
} else if (process.env.LARASSET_COMMAND === 'server') {
   require(__dirname + '/larasset-js/server.js');
}
