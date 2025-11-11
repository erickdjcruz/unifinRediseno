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
 * The dashboard container of the Omnichannel console.
 *
 * @class View.Layouts.Base.OmnichannelDashboardLayout
 * @alias SUGAR.App.view.layouts.BaseOmnichannelDashboardLayout
 * @extends View.Layout
 */
({
    className: 'omni-dashboard dashboard-pane',

    /**
     * Stores the index of the search tab
     */
    searchTabIndex: 0,

    /**
     * Maps the module to the appropriate tab index
     */
    moduleTabIndex: {
        Contacts: 1,
        Accounts: 2,
        Leads: 3,
        Cases: 4,
    },

    /**
     * Context models for tabs.
     * @property {Array}
     */
    tabModels: [],

    /**
     * Defines properties that are passed in to the quick-create drawer as
     * pre-populated data when the quick-create drawer is opened within the
     * context of an open Omnichannel console. The format is as follows:
     *
     * ```
     * populateLists: {
     *     *module name to pre-populate*: {
     *         *source module from dashboard*: {
     *             ...
     *             *target prepopulate data property*: *property name from source module's model*
     *             ...
     *         }
     *     }
     * }
     * ```
     */
    populateLists: {
        Cases: {
            Contacts: {
                primary_contact_id: 'id',
                primary_contact_name: 'name',
                account_id: 'account_id',
                account_name: 'account_name'
            }
        }
    },

    /**
     * Defines functions that should run after certain new models are created
     * from a quick-create drawer
     */
    postQuickCreateFunctions: {
        Cases: [
            '_setContactModelFromCaseModel'
        ]
    },

    /**
     * Holds the last searched collection on the search tab
     * We do this so that when you switch tabs, we can restore the previous collection when
     * returning to the search tab.
     * @property {Data.MixedBeanCollection}
     */
    searchCollection: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.tabModels = [];
        // this tells dashboard controller which dashboard to load
        this.context.set({
            layout: 'omnichannel',
            module: 'Dashboards'
        });
        this.context = this._getContext();

        this.before('tabbed-dashboard:switch-tab', function(params) {
            return this.triggerBefore('omni-dashboard:content-changed', {callback: params.callback});
        }, this);
    },

    /**
     * Util method to get context for layout
     *
     * @return {Object} context for layout
     * @private
     */
    _getContext: function() {
        return this.context.getChildContext({
            forceNew: true,
            layout: 'omnichannel',
            module: 'Dashboards',
            readonly: true
        });
    },

    /**
     * Override dashboard.js to remove dashboard header.
     * @inheritdoc
     */
    initComponents: function(components, context, module) {
        if (_.isArray(components) && components[0] && components[0].layout && components[0].layout.type &&
            components[0].layout.type === 'dashboard') {
            components[0].layout.components = this._getDashboardComponents();
        }
        return this._super('initComponents', [components, context, module]);
    },

    /**
     * Util to get dashboard components
     * @return {Array} Array of components representing dashboard metadata
     * @private
     */
    _getDashboardComponents: function() {
        return [
            {
                view: {
                    name: 'omnichannel-dashboard',
                    type: 'omnichannel-dashboard',
                    sticky: false
                }
            },
            {
                layout: 'dashlet-main'
            }
        ];
    },

    /**
     * Returns the tab index of the search tab
     *
     * @return {int|null} the tab index of the search tab, or null if it doesn't
     *                    exist
     */
    getSearchTabIndex: function() {
        return _.isNumber(this.searchTabIndex) ? this.searchTabIndex : null;
    },

    /**
     * Set parameters to pre-fill the search query in the search tab
     *
     * @param {Object} params the search parameters to set
     * @param {string} params.term the search term to set
     * @param {string} params.module_list the list of modules to search
     */
    setSearch: function(params) {
        params = params || {};
        if (params.term) {
            this.savedSearchTerm = params.term;
        }
        if (params.module_list) {
            if (!this.searchCollection) {
                this.searchCollection = app.data.createMixedBeanCollection();
            }
            this.searchCollection.selectedModules = params.module_list;
        }
    },

    /**
     * Set context model for a tab.
     * @param {number} tabIndex
     * @param {Object} model The new model
     */
    setModel: function(tabIndex, model) {
        this.tabModels[tabIndex] = model;
        // enable tab
        var tabbedDashboard = this._getTabbedDashboard();
        if (tabbedDashboard) {
            tabbedDashboard.setTabMode(tabIndex, true);
        }
    },

    /**
     * Change context model.
     * @param {number} tabIndex
     */
    switchModel: function(tabIndex) {
        if (this.tabModels[tabIndex]) {
            if (this.context.parent) {
                this.context.parent.set('rowModel', this.tabModels[tabIndex]);
            }
            if (this.context.children[tabIndex]) {
                this.context.set('module', this.context.children[tabIndex].get('module'));
            }
            // for interaction dashlets
            this.context.set('rowModel', this.tabModels[tabIndex]);
        }
    },

    /**
     * Change active tab.
     * @param {number} tabIndex
     */
    switchTab: function(tabIndex) {
        this.switchModel(tabIndex);
        var tabbedDashboard = this._getTabbedDashboard();

        if (tabbedDashboard && !_.isEmpty(tabbedDashboard.tabs)) {
            // The tabbed dashboard exists and its tabs have already been set
            tabbedDashboard.switchTab(tabIndex);
        } else {
            // The tabbed dashboard either does not exist or its tabs have not
            // yet been set. Keep track of the active tab here so that when the
            // tabs are set, we can switch to the correct tab when it is initialized
            this.initActiveTab = tabIndex;
        }
    },

    /**
     * Enable/disable tabs.
     */
    setTabModes: function() {
        var tabbedDashboard = this._getTabbedDashboard();
        if (tabbedDashboard && _.isArray(tabbedDashboard.tabs)) {
            var len = tabbedDashboard.tabs.length;
            if (this.configLayout) {
                tabbedDashboard.setTabMode(0, false);
            }
            for (let i = 1; i < len; i++) {
                if (app.acl.hasAccess('view',tabbedDashboard.tabs[i].module)) {
                    // enable tab if tabModel is set, otherwise disable it
                    tabbedDashboard.setTabMode(i, !_.isUndefined(this.tabModels[i]));
                } else {
                    // Hide the tab is the module has no view access instead of disabling it
                    this.setTabMode(i, false);
                }
            }
        }
    },

    /**
     *
     * Show/hide a tab.
     * @param {number} index The tab index
     * @param {boolean} mode True to enable, false to disbale
     */
    setTabMode: function(index, mode) {
        if (this.tabs && this.tabs[index]) {
            this.tabs[index].enabled = mode;
        }
        var $tab = this.$('a[data-index="' + index + '"]').closest('.tab');
        if ($tab) {
            mode ? $tab.removeClass('hidden') : $tab.addClass('hidden');
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        var tabbedDashboard = this._getTabbedDashboard();
        if (tabbedDashboard && !this._onTabEvent) {
            if (tabbedDashboard.context) {
                tabbedDashboard.context.on('change:activeTab', function(ctx) {
                    this.switchModel(ctx.get('activeTab'));
                }, this);
            }
            tabbedDashboard.on('render', this.setTabModes, this);
            this._onTabEvent = true;
        }
    },

    /**
     * Get 'omnichannel-dashboard' component.
     * @return {View.View|null}
     * @private
     */
    _getTabbedDashboard: function() {
        var tabbedDashboard = null;
        var dashboard = this.getComponent('dashboard');
        if (dashboard) {
            tabbedDashboard = dashboard.getComponent('omnichannel-dashboard');
        }
        return tabbedDashboard;
    },

    /**
     * Gets the index of the correct tab of the dashboard for the given model
     *
     * @param {Bean} model the model to get the tab index for
     * @return {int|null} the integer index of the tab if it exists, null otherwise
     */
    getTabIndexForModel: function(model) {
        var module = model.module || model.get('_module');
        return this.moduleTabIndex[module] || null;
    },

    /**
     * Gets the model stored in the given tab index of the dashboard
     *
     * @param {int} tabIndex The index of the tab to get the model from
     * @return {Bean|null} The model of the tab index if it exists; null otherwise
     */
    getModelForTabIndex: function(tabIndex) {
        return this.tabModels[tabIndex] || null;
    },

    /**
     * Builds and returns an object containing attribute data gathered from this
     * dashboard's various tab models as defined in populateLists
     *
     * @param {string} targetModule the module to get prepopulate data for
     * @return {Object} The set of prepopulate data attributes
     */
    getModelPrepopulateData: function(targetModule) {
        var data = {};

        // Get the source modules defined for the given module
        var sources = this.populateLists[targetModule];

        // For each of the source modules, pull the defined data from that module's tab
        _.each(sources, function(populateList, sourceModule) {
            var model = this.getModelForTabIndex(this.moduleTabIndex[sourceModule]);
            if (!_.isEmpty(model)) {
                data = _.extend(data, this._getPopulateListFromModel(populateList, model));
            }
        }, this);

        return data;
    },

    /**
     * Returns a set of source fields mapped to the values of target fields in
     * a model's attributes
     *
     * @param {Object} populateList a mapping of {target attribute => source attribute}
     * @param {Bean} model the model to get source attributes from
     * @return {Object} a mapping of {target attribute => source attribute value from model}
     * @private
     */
    _getPopulateListFromModel: function(populateList, model) {
        return _.mapObject(populateList, function(sourceField) {
            return model.get(sourceField);
        });
    },

    /**
     * Handles any special functionality that should be run after a model is
     * quick-created from the Omnichannel console, as defined in the
     * postQuickCreateFunctions variable
     *
     * @param {Bean} createdModel the model that came from the quick-create drawer
     */
    postQuickCreate: function(createdModel) {
        // Get the list of functions to run based on the module of the created model
        var module = createdModel.module || createdModel.get('_module');
        var functionsList = this.postQuickCreateFunctions[module];

        // Run the functions in the list, passing the created model in
        _.each(functionsList, function(functionName) {
            if (_.isFunction(this[functionName])) {
                this[functionName](createdModel);
            }
        }, this);
    },

    /**
     * Given a Case model, sets the Contact model of this dashboard to the
     * record linked by the primary_contact_id field of the case
     *
     * @param model The case model
     */
    _setContactModelFromCaseModel: function(model) {
        var module = model.module || model.get('_module');
        if (module !== 'Cases') {
            return;
        }

        // Get the ID of the primary Contact record for the created Case
        var newContactId = model.get('primary_contact_id');

        // Get the ID of the Contact currently stored in the Contacts tab
        var oldContact = this.getModelForTabIndex(this.moduleTabIndex.Contacts);
        var oldContactId = !_.isEmpty(oldContact) ? oldContact.get('id') : null;

        // If the new Contact ID is a different record, fetch it and replace the
        // one that is currently stored in the Contacts tab
        if (newContactId && newContactId !== oldContactId) {
            var contactBean = app.data.createBean('Contacts', {id: newContactId});
            contactBean.fetch({
                success: _.bind(function() {
                    this.setModel(this.getTabIndexForModel(contactBean), contactBean);
                }, this)
            });
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.searchCollection) {
            this.searchCollection.off();
            this.searchCollection = null;
        }
        this._super('_dispose');
    }
})
