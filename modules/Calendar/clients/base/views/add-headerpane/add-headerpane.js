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
/**
 * @class View.Views.Base.Calendar.CalendarAddHeaderpaneView
 * @alias SUGAR.App.view.views.BaseCalendarAddHeaderpaneView
 * @extends View.Views.Base.HeaderpaneView
 */
({
    extendsFrom: 'HeaderpaneView',

    events: {
        'click [name=cancel_button]': '_cancel'
    },

    /**
     * Close the drawer.
     *
     * @private
     */
    _cancel: function() {
        app.drawer.close();
    }
});
