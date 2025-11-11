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
 * Action button configuration settings view
 *
 * @class View.Views.Base.AdministrationActionbuttonDisplaySettingsRecordView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonDisplaySettingsRecordView
 * @extends View.View
 */
({
    events: {
        'change [data-fieldname=buttonType]': 'buttonTypeChanged',
        'change [data-fieldname=buttonSize]': 'buttonSizeChanged',
        'change [data-fieldname=showFieldLabel]': 'showFieldLabel',
        'change [data-fieldname=showInRecordHeader]': 'showInRecordHeader',
        'change [data-fieldname=hideOnEdit]': 'hideOnEdit',
        'change input.default-action': 'onChangeDefaultHandler',
    },
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._updateDisplaySettings();
        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this._settings = options.context.get('model').get('data').settings;

        if (Object.keys(this._settings).length === 0) {
            this._settings = {
                type: 'button',
                size: 'default',
                showFieldLabel: false,
                showInRecordHeader: false,
                hideOnEdit: false,
                displayOnFocusDashboard: false,
            };
        }
    },

    /**
     * Updates configuration and re-renders preview
     *
     */
    _updateDisplaySettings: function() {
        var ctxModel = this.context.get('model');
        var buttonsData = ctxModel.get('data');
        buttonsData.settings = this._settings;

        this.context.trigger('update-buttons-preview', buttonsData);
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

        this.$('.ab-admin-select').select2();
        this._showHideButtonSizeController();
    },

    /**
     * Handle button type change event
     *
     * @param {UIEvent} e
     */
    buttonTypeChanged: function(e) {
        this._settings.type = e.currentTarget.value;

        this._updateDisplaySettings();

        this._showHideButtonSizeController();
    },

    /**
     * Handle button size change event
     *
     * @param {UIEvent} e
     */
    buttonSizeChanged: function(e) {
        this._settings.size = e.currentTarget.value;

        this._updateDisplaySettings();
    },

    /**
     * Update field label visibility property
     *
     * @param {UIEvent} e
     */
    showFieldLabel: function(e) {
        this._settings.showFieldLabel = e.currentTarget.checked;

        this._updateDisplaySettings();
    },

    /**
     * Update record header visibility property
     *
     * @param {UIEvent} e
     */
    showInRecordHeader: function(e) {
        this._settings.showInRecordHeader = e.currentTarget.checked;

        this._updateDisplaySettings();
    },

    /**
     * Update record edit mode visibility property
     *
     * @param {UIEvent} e
     */
    hideOnEdit: function(e) {
        this._settings.hideOnEdit = e.currentTarget.checked;

        this._updateDisplaySettings();
    },

    /**
     * Update field value by
     *
     * @param {UIEvent} e
     */
    onChangeDefaultHandler: function(e) {
        const fieldName = e.currentTarget.getAttribute('data-fieldname');
        this._settings[fieldName] = e.currentTarget.checked;
    },

    /**
     * Toggle button size selector based on action button type
     *
     */
    _showHideButtonSizeController: function() {
        if (this._settings.type === 'button') {
            this.$('[data-container=button-size]').show();
        } else {
            this.$('[data-container=button-size]').hide();
        }
    },
});
