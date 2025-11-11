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
(function(app) {
    app.events.on('router:init', function(router) {
        const routes = [
            {
                name: 'mainCalendar',
                route: 'Calendar/center',
                callback: function() {
                    app.controller.loadView({
                        'layout': 'main-scheduler',
                        'module': 'Calendar'
                    });
                }
            }
        ];

        app.router.addRoutes(routes);
    });
})(SUGAR.App);
