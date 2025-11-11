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

(function resourcesLoader(app) {
    /**
     * Dynamically charge scripts and css files
     *
     * @method loadjscssfile
     * @param  {string} filename [description]
     * @param  {string} filetype js/css
     */
    function loadjscssfile(filename, filetype) {
        let fileref;
        if (filetype === 'js') {
            //if filename is a external JavaScript file
            fileref = document.createElement('script');
            fileref.setAttribute('type', 'text/javascript');
            fileref.setAttribute('src', filename);
        } else if (filetype === 'css') {
            //if filename is an external CSS file
            fileref = document.createElement('link');
            fileref.setAttribute('rel', 'stylesheet');
            fileref.setAttribute('type', 'text/css');
            fileref.setAttribute('href', filename);
        }
        if (typeof fileref !== 'undefined') {
            $('head').append(fileref);
        }
    }

    loadjscssfile('include/javascript/kendo/kendo.office365.min.css', 'css');
})(SUGAR.App);
