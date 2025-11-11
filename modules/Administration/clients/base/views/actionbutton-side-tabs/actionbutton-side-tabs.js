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
 * Action button tab view
 *
 * @class View.Views.Base.AdministrationActionbuttonSideTabsView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonSideTabsView
 * @extends View.View
 */
({
    events: {
        'click li [data-tabId]': 'tabButtonClicked',
    },

    /**
     * @inheritdoc
     */
     initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initial setup of properties
     *
     */
    _initProperties: function() {
    },

    /**
     * Context model event registration
     *
     */
    _registerEvents: function() {
    },

    /**
     * Handler for tab selection
     *
     * @param {UIEvent} e
     *
     */
     tabButtonClicked: function(e) {
        var tabId = e.currentTarget.dataset.tabid;

        this.context.get('model').trigger('update:side-pane:view', tabId);
    },
})
