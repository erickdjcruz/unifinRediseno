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
 * @class View.Views.Base.SugarliveConfigPreviewView
 * @alias SUGAR.App.view.views.BaseSugarliveConfigPreviewView
 * @extends View.Fields.Base.ConfigPanelView
 */
({
    extendsFrom: 'ConfigPanelView',

    /**
     * A list of input types that should be made readonly on the preview.
     */
    inputTypes: ['input', 'select', 'checkbox', 'textarea'],

    /**
     * Fields to be displayed in omnichannel detail panel.
     * @property [Array]
     */
    summaryFields: [
        'LBL_INVITEES',
        'LBL_LIST_RELATED_TO',
    ],

    /**
     * Shows the active tab index used for when switching tabs.
     */
    activeTabIndex: 0,

    /**
     * Information (titles and fields) to be displayed on distinct preview tabs.
     */
    tabs: [],

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.collection.on('add remove reset preview', this.render, this);
    },

    /**
     * Returns the list of modules the user has access to
     * and are supported.
     *
     * @return {Array} The list of module names.
     */
    getAvailableModules: function() {
        var selectedModules = this.context.get('enabledModules');
        return _.filter(selectedModules, function(module) {
            return !_.isEmpty(app.metadata.getModule(module));
        });
    },

    /**
     * Based on the config saved in the models it will collect the necessary
     * details for displaying a preview.
     */
    setPreviewTabs: function() {
        this.tabs = {};
        this.tabsLength = 0;
        var availableModules = this.getAvailableModules();

        _.each(availableModules, function(module) {
            var fieldList = document.querySelector('.drawer.active #' + module + '-side .field-list') || [];
            var selectedFields = _.map(fieldList.children, function(li) {
                return li.getAttribute('fieldname');
            });
            this.tabsLength++;
            this.tabs[module] = {};
            this.tabs[module].module = module;
            this.setPreviewTitle(module);
            this.setPreviewFields(module, selectedFields);
        }, this);
    },

    /**
     * It will get the title texts to be displayed on the previews.
     *
     * @param {string} module The name of a module for which the titles should be retrieved.
     */
    setPreviewTitle: function(module) {
        var tab = this.tabs[module];
        moduleName = app.lang.getModuleName(module) + ' ';
        tab.title = moduleName + app.lang.get('LBL_SUGARLIVE_SUMMARY_PREVIEW', this.module);
        tab.detailTitle = moduleName + app.lang.get('LBL_SUGARLIVE_PREVIEW', this.module);
    },

    /**
     * It will compile a list of field definitions to render.
     *
     * @param {string} module The name of the module for which the field list is compiled.
     * @param {Array} fields A list of field names that should appear on the preview.
     */
    setPreviewFields: function(module, fields) {
        var tab = this.tabs[module];
        var metaField = app.utils.deepCopy(app.metadata.getField({module: module}));
        // convert from vardefs field type to widget field type, this also patches labels
        app.metadata._patchFields(module, app.metadata.getModule(module), metaField);
        tab.fields = [];
        tab.model = app.data.createBean(module);
        _.each(fields, function(fieldName) {
            tab.fields.push({
                name: fieldName,
                type: metaField[fieldName].type,
                label: metaField[fieldName].label,
                // Do not look for the related module (relate and custom relate types.)
                module: ' ',
                // Do not make requests for enum and extended enum types.
                options: []
            });
        });
    },

    /**
     * After render make all preview fields read only and remove any content that appears.
     */
    disableInputs: function() {
        _.each(this.inputTypes, function(tagName) {
            var inputs = this.$el.find('.omni-cell ' + tagName);
            _.each(inputs, function(field) {
                field.setAttribute('readonly', true);
                field.setAttribute('placeholder', '');
                field.className += ' edit-disabled';
            });
        }, this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this.setPreviewTabs();
        this._super('render');
        this.disableInputs();
        this.initTabs();
    },

    /**
     * Initialize the tabs with the active one.
     */
    initTabs: function() {
        this.$('#tabs').tabs({
            active: this.context.get('activeTabIndex'),
            activate: _.bind(this.setTabsDisplay, this)
        });
    },

    /**
     * It will set a tab as active. When selecting another tab,
     * show/hide the corresponding side pane div accordingly.
     */
    setTabsDisplay: function() {
        var index = this.$('#tabs').tabs('option', 'active');
        this.context.set('activeTabIndex', index);
        var sidePanes = this._getSidePanes();
        if (sidePanes) {
            sidePanes.hide();
            $(sidePanes[index]).css('display', 'flex');
        }
    },

    /**
     * Util to retrieve side panes from this config drawer layout
     * @private
     */
    _getSidePanes: function() {
        var config = this.closestComponent('config-drawer');
        if (config) {
            return config.$('.config-side-pane-all .config-side-pane');
        }
    }
});
