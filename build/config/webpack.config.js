/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

module.exports = {

    context : __dirname + '/../../src/js',

    entry: "./entry.js",

    output: {

        filename: "bundle.js"
    },

    module: {

        loaders: [
            { test: /\.css$/, loader: "style!css" }
        ]
    },

    resolveLoader: {

        fallback: __dirname + "/../src/npm/node_modules"
    }
};