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

// NOTE: This file should only be used to generate a built tailwind.css file for the Web Upgrader.
// TODO clean up and only include required dependencies. A working model to further remove the dependency on ugprade.css

const SugarColorPalette = require('./maple-syrup/build/tailwind/sugar-tw-color-palette');

module.exports = {
    blocklist: [
        'collapse', // Collides with Bootstrap collapse plugin
    ],
    content: [
        './styleguide/tailwind.css',
        './modules/UpgradeWizard/upgrade_screen.php',
    ],
    darkMode: ['class', '.sugar-dark-theme'],
    theme: {
        colors: SugarColorPalette,
        borderRadius: {
            'none': '0',
            'sm': '0.125rem',
            DEFAULT: '0.25rem',
            'md': '0.425rem',
            'lg': '0.6rem',
            'xl': '0.75rem',
            '2xl': '1rem',
            '3xl': '1.5rem',
            'full': '9999px'
        },
        boxShadow: {
            'none': '0 0 0 var(--shadow-color)',
            DEFAULT: '0 2px 2px var(--shadow-color), 0 0 2px var(--shadow-color)',
            'md': '0 2px 4px var(--shadow-color)',
            'lg': '0 2px 8px var(--shadow-color)',
            'xl': '0 2px 9px var(--shadow-color)'
        },
        extend: {},
    },
    plugins: [],
    corePlugins: {
        preflight: false, // Enable once more of our own preflight and reset styles are cleaned up
    }
};
