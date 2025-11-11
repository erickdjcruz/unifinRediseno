/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

const path = require('path');

const devMode = process.env.DEV;

module.exports = {
    mode: devMode ? 'development' : 'production',
    optimization: {
        minimize: !devMode,
    },
    entry: {
        'grp-sidecar': 'entry/grp-sidecar.js',
        'grp-bootstrap': 'entry/grp-bootstrap.js',
        'bootstrap': 'entry/bootstrap.js',
    },
    module: {
        rules: [{
            test: /\.js$/,
            exclude: /node_modules/,
            use: [{
                loader: 'babel-loader',
                options: {
                    presets: [
                        ['env', {
                            targets: {
                                browsers: [
                                    'last 1 chrome version',
                                    'last 1 firefox version',
                                    'last 1 safari version',
                                    'last 1 edge version',
                                    'ie 11',
                                ],
                            },
                        }],
                    ],
                },
            }],
        }],
    },
    output: {
        path: path.resolve(__dirname, 'jssource/src/build'),
        filename: '[name].min.js',
    },
    resolve: {
        modules: [
            path.join(__dirname, 'jssource/src'),
            path.join(__dirname, 'node_modules'),
        ],
    },
};
