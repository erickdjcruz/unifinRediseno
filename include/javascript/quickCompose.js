
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

(function(){
    var JSON = YAHOO.lang.JSON;

    SUGAR.quickCompose = {};

    SUGAR.quickCompose = function() {
        return {
            parentPanel: null,

            /**
             * Display a loading pannel and start retrieving the quick compose requirements.
             * @method init
             * @param {Array} o Options containing compose package and full return url.
             * @return {} none
             **/
            init: function(o) {
                var app = parent.SUGAR.App;
                var view = app.controller.layout.getComponent('bwc');

                if (view) {
                    view.openComposeEmailDrawer(o.composePackage || {});
                }

                return false;
            }
        };
    }();
})();
