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
 * Open URL action configuration view
 *
 * @class View.Views.Base.AdministrationActionbuttonAssignRecordView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonAssignRecordView
 * @extends View.View
 */
({
    events: {
        'change input[type="checkbox"][data-fieldname="calculated"]': 'calculatedChanged',
        'change textarea[data-fieldname="url"]': 'urlChanged',
    },

    /**
    * @inheritdoc
    */
    initialize: function(options) {
        this._beforeInit(options);
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        this._buttonId = options.buttonId;
        this._actionId = options.actionId;

        if (options.actionData && options.actionData.properties &&
            Object.keys(options.actionData.properties).length !== 0) {
            this._properties = options.actionData.properties;
        } else {
            this._properties = {
                url: '',
                calculated: false,
                formula: ''
            };
        }
        this._properties.calculated = app.utils.isTruthy(this._properties.calculated);

        this._formulaBuilder = null;
    },

    /**
     * Property initialization, nothing to do for this view
     *
     */
    _initProperties: function() {
    },

    /**
     * Context event registration, nothing to do for this view
     *
     */
    _registerEvents: function() {
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this._properties.calculated) {
            this._createFormulaBuilder();
        }
    },

    /**
     * View setup, nothing to do for this view
     *
     */
    setup: function() {
    },

    /**
     * Return action configuration
     *
     * @return {Object}
     */
    getProperties: function() {
        return this._properties;
    },

    /**
     * Handler for calculated URL checkbox selection
     *
     * @param {UIEvent} e
     *
     */
    calculatedChanged: function(e) {
        this._properties.calculated = e.currentTarget.checked;

        this._updateActionProperties();
        this.render();
    },

    /**
     * Handler for URL value change
     *
     * @param {UIEvent} e
     *
     */
    urlChanged: function(e) {
        this._properties.url = e.currentTarget.value;

        this._updateActionProperties();
    },

    /**
     * Handler for URL formula change
     *
     * @param {Object} data
     *
     */
    formulaChanged: function(data) {
        this._properties.formula = data;

        this._updateActionProperties();
    },

    /**
     * Some basic validation of properties
     *
     * @return {bool}
     */
    canSave: function() {
        if (this._properties.calculated) {
            return this._formulaBuilder.isValid();
        }

        return true;
    },

    /**
     * Create the formula builder sidecar field
     *
     */
    _createFormulaBuilder: function() {
        this.$('[data-fieldname="url"]').hide();

        this._disposeFormulaBuilder();
        var fbContainer = this.$('[data-fieldname="formula"]');

        fbContainer.empty();
        fbContainer.toggleClass('hidden', false);

        this._formulaBuilder = app.view.createField({
            def: {
                type: 'formula-builder',
                name: 'ABCustomAction'
            },
            view: this,
            viewName: 'edit',
            targetModule: this.options.context.get('model').get('module'),
            callback: _.bind(this.formulaChanged, this),
            formula: this._properties.formula
        });

        this._formulaBuilder.render();

        fbContainer.append(this._formulaBuilder.$el);
    },

    /**
     * Update action properties in context
     *
     */
    _updateActionProperties: function() {
        var ctxModel = this.context.get('model');

        var buttonsData = ctxModel.get('data');
        buttonsData.buttons[this._buttonId].actions[this._actionId].properties = this._properties;

        // update action data into the main data container
        ctxModel.set('data', buttonsData);
    },

    /**
     * Dispose formula builder field
     *
     */
    _disposeFormulaBuilder: function() {
        if (this._formulaBuilder) {
            this._formulaBuilder.dispose();

            this._formulaBuilder = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeFormulaBuilder();

        this._super('_dispose');
    },
});
