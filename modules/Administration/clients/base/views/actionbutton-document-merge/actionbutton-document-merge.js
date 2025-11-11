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
 * Document Merge action configuration view
 *
 * @class View.Views.Base.AdministrationActionbuttonDocumentMergeView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonDocumentMergeView
 * @extends View.View
 */
 ({
    events: {
        'change input[type=checkbox][data-fieldname=pdf]': 'pdfChanged',
    },

    /**
    * @inheritdoc
    */
    initialize: function(options) {
        this._beforeInit(options);
        this._super('initialize', [options]);

        this.isSellServe = app.user.hasSellServeLicense();

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
                pdf: false,
            };
        }

        this._properties.pdf = app.utils.isTruthy(this._properties.pdf);
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
    },

    /**
     * Handle update of selected PDF Template change
     *
     * @param {UIEvent} e
     *
     */
    pdfChanged: function(e) {
        this._properties.pdf = e.currentTarget.checked;

        this._updateActionProperties();
        this.render();
    },

    /**
     * Some basic validation of properties
     *
     * @return {bool}
     */
    canSave: function() {
        if (!this._properties.id) {
            app.alert.show('alert_actionbutton_document_merge_nodata', {
                level: 'error',
                title: app.lang.get('LBL_ACTIONBUTTON_INVALID_DATA'),
                messages: app.lang.get('LBL_ACTIONBUTTON_SELECT_TEMPLATE'),
                autoClose: true,
                autoCloseDelay: 5000,
            });

            return false;
        }

        return true;
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
                pdf: this._properties.pdf,
            };

            this._updateSelect2View();
            this._updateActionProperties();
        }
    },

    /**
     * Update Select2 selection with configured action
     *
     */
    _updateSelect2View: function() {
        if (this.disposed) {
            return;
        }

        this.$('[name="dm_template_name"]').select2('data', {
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

        var moduleName = 'DocumentTemplates';

        this.model.set({
            dm_template_name: this._properties.name,
            dm_template_id: this._properties.id,
            name: this._properties.name
        });

        this._templateSelectionField = app.view.createField({
            def: {
                type: 'relate',
                module: moduleName,
                name: 'dm_template_name',
                rname: 'name',
                id_name: 'dm_template_id',
                initial_filter_label: 'LBL_ACTIONBUTTON_DOCUMENT_MERGE',
                filter_populate: {
                    'template_module': {
                        '$in': [this.model.get('module')]
                    }
                },
            },
            view: this,
            viewName: 'edit',
        });

        this._templateSelectionField.render();
        this._templateSelectionField.setValue = _.bind(this.setValue, this);

        var templateContainer = this.$('[data-fieldname="dm_template_name"]');

        templateContainer.empty();
        templateContainer.append(this._templateSelectionField.$el);

        if (this._properties.id) {
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
     * @inheritdoc
     */
    _dispose: function() {
        this.disposeTemplateSelectField();

        this._super('_dispose');
    },
});
