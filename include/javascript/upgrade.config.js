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

module.exports = {
    yui3: {
        copyFiles: [
            {
                from: [
                    'node_modules/yui/**/*',
                    '!node_modules/yui/**/debug.js',
                    '!node_modules/yui/**/*-debug.js',
                    '!node_modules/yui/node_modules/**',
                ],
                to: 'include/javascript/yui3/build',
            },
        ],
        removeBefore: [
            'include/javascript/yui3/build',
        ],
    },
    tinymce: {
        copyFiles: [
            {
                from: [
                    'node_modules/tinymce/**/*',
                    '!node_modules/tinymce/skins/ui/tinymce-5/**',
                    '!node_modules/tinymce/skins/ui/tinymce-5-dark/**',
                    '!node_modules/tinymce/skins/content/document/**',
                    '!node_modules/tinymce/skins/content/tinymce-5/**',
                    '!node_modules/tinymce/skins/content/tinymce-5-dark/**',
                    '!node_modules/tinymce/skins/content/writer/**',
                    '!node_modules/tinymce/tinymce.js',
                ],
                to: 'include/javascript/tinymce6',
            },
        ],
        removeBefore: [
            'include/javascript/tinymce6',
        ],
    },
};
