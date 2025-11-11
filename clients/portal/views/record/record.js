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
 * Portal Record view.
 *
 * @class View.Views.Portal.PortalRecordView
 * @alias SUGAR.App.view.views.PortalRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',
    sidebarClosed: false,
    unwantedPlugins: ['Pii'],

    initialize: function(options) {
        // remove pii plugin
        this.plugins = _.difference(this.plugins, this.unwantedPlugins);
        this._super("initialize", [options]);
        // Once the sidebartoggle is rendered we close the sidebar so the arrows are updated SP-719
        app.controller.context.on("sidebarRendered", this.closeSidebar, this);
    },
    closeSidebar: function () {
        if (!this.sidebarClosed) {
            app.controller.context.trigger('toggleSidebar');
            this.sidebarClosed = true;
        }
    }
})
