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
 * @class View.Layouts.Base.AdministrationAdministrationLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationAdministrationLayout
 * @extends View.Layout
 */
({
    /**
     * Admin Panels metadata
     */
    adminPanelDefs: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.fetchAdminPanelDefs();
    },

    /**
     * Fetch Admin Panels metadata
     */
    fetchAdminPanelDefs: function() {
        var url = app.api.buildURL('Administration/adminPanelDefs');
        app.api.call('read', url, null, {
            success: _.bind(function(data) {
                this.handleFetchAdminPanelDefsSuccess(data);
            }, this)
        });
    },

    /**
     * Handle a successful fetch of Admin Panels metadata
     *
     * @param data
     */
    handleFetchAdminPanelDefsSuccess: function(data) {
        this.adminPanelDefs = data;

        this.trigger('admin:panel-defs:fetched');
    },

    /**
     * A helper function to get adminPanelDefs so child components
     * do not access the property directly
     *
     * @return array
     */
    getAdminPanelDefs: function() {
        return this.adminPanelDefs || [];
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this.$('input[data-action="search"]').focus();
    },
})
