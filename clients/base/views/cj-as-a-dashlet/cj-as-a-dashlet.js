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
 * @class View.Views.Base.CJAsADashlet
 * @alias SUGAR.App.view.views.CJAsADashlet
 * @extends View.View
 */
({
    plugins: ['Dashlet', 'CJAsPanelOrTab'],

    dashletError: false,
    className: 'customer-journey-as-a-dashlet',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._noAccessTemplate = app.template.get(`${ this.name }.noaccess`);
    },

    /**
     * Return current module in which we are going to add the dashlet
     *
     * @return {string|undefined}
     */
    _getModule: function() {
        let module = '';

        if (
            !_.isEmpty(this.context) &&
            !_.isEmpty(this.context.parent) &&
            !_.isEmpty(this.context.parent.parent) &&
            _.isFunction(this.context.parent.parent.get)
        ) {
            module = this.context.parent.parent.get('module');
        } else if (
            !_.isEmpty(this.options) &&
            !_.isEmpty(this.options.context) &&
            !_.isEmpty(this.options.context.parent) &&
            !_.isEmpty(this.options.context.parent.parent) &&
            _.isFunction(this.options.context.parent.parent.get)
        ) {
            module = this.options.context.parent.parent.get('module');
        } else {
            module = this.module;
        }

        return module;
    },

    /**
     * Check if parent layout is focusDrawer or not multi-line
     * for service and renewal console.
     *
     * @param {string} dashboardType
     * @return {boolean}
     */
    _isMatchedDashBoardType: function(dashboardType = '') {
        if (_.isEmpty(dashboardType)) {
            return false;
        }

        let isMatched = false;

        if (
            !_.isEmpty(this.context) &&
            !_.isEmpty(this.context.parent) &&
            !_.isEmpty(this.context.parent.parent) &&
            _.isFunction(this.context.parent.parent.get) &&
            _.isEqual(this.context.parent.parent.get('layout'), dashboardType)
        ) {
            isMatched = true;
        } else if (
            !_.isEmpty(this.options) &&
            !_.isEmpty(this.options.context) &&
            !_.isEmpty(this.options.context.parent) &&
            !_.isEmpty(this.options.context.parent.parent) &&
            _.isFunction(this.options.context.parent.parent.get) &&
            _.isEqual(this.options.context.parent.parent.get('layout'), dashboardType)
        ) {
            isMatched = true;
        }

        return isMatched;
    },

    /**
     * Load the CJ in dashlet
     *
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // When adding dashlet
        if (this.meta && this.meta.config) {
            return false;
        } else {
            if (this._isMatchedDashBoardType('focus') || this._isMatchedDashBoardType('multi-line')) {
                this._setCurrentModel();
                this._loadCjAsDashlet();
            }
        }

        if (this.dashletError) {
            this.$el.html(this._noAccessTemplate());
            return false;
        }
    },

    /**
     * Set the model according to the view
     */
    _setCurrentModel: function() {
        let model = {};

        if (
            !_.isEmpty(this.context) &&
            !_.isEmpty(this.context.parent) &&
            !_.isEmpty(this.context.parent.parent) &&
            !_.isEmpty(this.context.parent.parent.get('rowModel'))
        ) {
            model = this.context.parent.parent.get('rowModel');
        } else if (
            !_.isEmpty(this.layout) &&
            !_.isEmpty(this.layout.context) &&
            !_.isEmpty(this.layout.context.parent) &&
            !_.isEmpty(this.layout.context.parent.parent) &&
            !_.isEmpty(this.layout.context.parent.parent.get('rowModel'))
        ) {
            model = this.layout.context.parent.parent.get('rowModel');
        } else if (
            !_.isEmpty(this.options) &&
            !_.isEmpty(this.options.context) &&
            !_.isEmpty(this.options.context.parent) &&
            !_.isEmpty(this.options.context.parent.parent) &&
            !_.isEmpty(this.options.context.parent.parent.get('rowModel'))
        ) {
            model = this.options.context.parent.parent.get('rowModel');
        } else if (
            !_.isEmpty(this.options) &&
            !_.isEmpty(this.options.layout) &&
            !_.isEmpty(this.options.layout.context) &&
            !_.isEmpty(this.options.layout.context.parent) &&
            !_.isEmpty(this.options.layout.context.parent.parent) &&
            !_.isEmpty(this.options.layout.context.parent.parent.get('rowModel'))
        ) {
            model = this.options.layout.context.parent.parent.get('rowModel');
        } else {
            model = this.model;
        }

        this.model = model;
    },

    /**
     * Handler for refresh button or dashlet refresh click
     */
    refreshClicked: function() {
        this.render();
    },
});
