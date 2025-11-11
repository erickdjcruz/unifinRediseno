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
 * Compose Email action configuration view
 *
 * @class View.Views.Base.AdministrationActionbuttonComposeEmailView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonComposeEmailView
 * @extends View.View
 */
({
    events: {
        'change input[type=checkbox][data-fieldname=bpm]': 'bpmChanged',
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

        if (options.actionData &&
            options.actionData.properties &&
            Object.keys(options.actionData.properties).length !== 0) {
            this._properties = options.actionData.properties;
        } else {
            this._properties = {
                id: '',
                name: '',
                emailToFormula: '',
                pmse: false,
            };
        }

        this._properties.pmse = app.utils.isTruthy(this._properties.pmse);
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

        this._createSelection();
        this._createFormulaBuilder();
    },

    /**
     * Handle update of selected PMSE Email Template change
     *
     * @param {UIEvent} e
     *
     */
    bpmChanged: function(e) {
        this._properties = {
            id: '',
            name: '',
            pmse: e.currentTarget.checked
        };

        this._updateActionProperties();
        this.render();
    },

    /**
     * Handle Recipient formula change
     *
     * @param {Object} data
     *
     */
    formulaChanged: function(data) {
        this._properties.emailToFormula = data;

        this._updateActionProperties();
    },

    /**
     * Some basic validation of properties
     *
     * @return {bool}
     */
    canSave: function() {
        return this._formulaBuilder.isValid();
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
     * Update action properties & UI based on selection
     *
     * @param {Object} selection
     *
     */
    setValue: function(selection) {
        if (selection) {
            this._properties = {
                id: selection.id,
                name: selection.name,
                pmse: this._properties.pmse,
                emailToFormula: this._properties.emailToFormula
            };

            this._updateSelect2View();
            this._updateActionProperties();
        }
    },

    /**
     * Create the formula builder sidecar field
     *
     */
    _createFormulaBuilder: function() {
        this.disposeFormulaBuilderField();

        var formulaContainer = this.$('div[data-fieldname="formula"]');

        formulaContainer.empty();

        this._formulaBuilder = app.view.createField({
            def: {
                type: 'formula-builder',
                name: 'ABCustomAction'
            },
            view: this,
            viewName: 'edit',
            targetModule: this.options.context.get('model').get('module'),
            callback: _.bind(this.formulaChanged, this),
            formula: this._properties.emailToFormula
        });

        this._formulaBuilder.render();

        formulaContainer.append(this._formulaBuilder.$el);
    },

    /**
     * Update Select2 selection with configured action
     *
     */
    _updateSelect2View: function() {
        if (this.disposed) {
            return;
        }

        this.$('[name="template_name"]').select2('data', {
            id: this._properties.id,
            text: this._properties.name
        });
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
     * Create relate field against Users module
     *
     */
    _createSelection: function() {
        this.disposeTemplateSelectField();

        var moduleName = this._properties.pmse ? 'pmse_Emails_Templates' : 'EmailTemplates';

        this.model.set({
            template_name: this._properties.name,
            template_id: this._properties.id,
            name: this._properties.name
        });

        this._templateSelectionField = app.view.createField({
            def: {
                type: 'relate',
                module: moduleName,
                name: 'template_name',
                rname: 'name',
                id_name: 'template_id'
            },
            view: this,
            viewName: 'edit',
        });

        this._templateSelectionField.render();
        this._templateSelectionField.setValue = _.bind(this.setValue, this);

        var templateContainer = this.$('[data-fieldname="template"]');

        templateContainer.empty();
        templateContainer.append(this._templateSelectionField.$el);

        if (this._properties.id !== '') {
            this._updateSelect2View();
        }
    },

    /**
     * Dipose the sidecar relate field created for template selection
     *
     */
    disposeTemplateSelectField: function() {
        if (this._templateSelectionField) {
            this._templateSelectionField.dispose();
            this._templateSelectionField = null;
        }
    },

    /**
     * Dispose the formula builder field created for recipient calculation
     *
     */
    disposeFormulaBuilderField: function() {
        if (this._formulaBuilder) {
            this._formulaBuilder.dispose();
            this._formulaBuilder = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.disposeTemplateSelectField();
        this.disposeFormulaBuilderField();

        this._super('_dispose');
    },
});
