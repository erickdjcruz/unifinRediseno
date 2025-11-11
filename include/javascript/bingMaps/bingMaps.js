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
(function() {
    const maps = {
        getBingPreviewUrl: function getBingScriptUrl() {
            return 'https://www.bing.com/mapspreview/sdk/mapcontrol';
        },

        loadScript: function(src) {
            let scriptEl = document.createElement('script');

            scriptEl.type = 'text/javascript';
            scriptEl.src = src;

            scriptEl = this._loadScriptErrorHandling(scriptEl);

            document.head.append(scriptEl);
        },

        _loadScriptErrorHandling: function(script) {
            script.addEventListener('error', function errorLoadScript() {
                reject(new Error('Error loading' + script));
            });

            return script;
        }
    };

    const bingUrl = maps.getBingPreviewUrl();

    maps.loadScript(bingUrl);
}());
