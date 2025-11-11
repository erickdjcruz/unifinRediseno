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
 * Parent field view selection
 *
 * @class View.Views.Base.AdministrationActionbuttonPreviewRecordView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonPreviewRecordView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this._encodeData = options.context.get('model').get('encodeData');
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this.context.on('update-buttons-preview', this._createPreview, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._createPreview(this.context.get('model').get('data'));
    },

    /**
     * Initialize and render an ActionButton based on current configuration
     *
     * @param {Object} buttonsData
     */
    _createPreview: function(buttonsData) {
        const data = this._getPreparedButtonsData(buttonsData);

        var metadata = JSON.stringify(this._encodeData(data, true));

        var previewContainer = this.$('.ab-admin-preview-container');

        previewContainer.empty();

        this._disposeButton();

        this.buttonPreview = app.view.createField({
            def: {
                type: 'actionbutton',
                name: 'PreviewButton',
                options: metadata
            },
            view: this,
            viewName: 'detail',
        });

        this.buttonPreview.render();

        previewContainer.append(this.buttonPreview.$el);
    },

    /**
     * Removes actions and dependencies from buttons
     * 
     * @param {Object} buttonsData 
     */
    _getPreparedButtonsData: function(buttonsData) {
        var data = app.utils.deepCopy(buttonsData);

        // remove dependencies and actions
        data.buttons = _.each(data.buttons, function removeDep(buttonData, id) {
            buttonData.properties.isDependent = false;
            buttonData.actions = {};
        });

        // if there are no settings yet applied, set default ones
        if (Object.keys(data.settings).length < 1) {
            data.settings = {
                type: 'button',
                size: 'default'
            };
        }

        return data;
    },

    /**
     * Dispose the current button field
     *
     */
    _disposeButton: function() {
        if (this.buttonPreview && this.buttonPreview.dispose) {
            this.buttonPreview.dispose();
            this.buttonPreview = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeButton();

        this._super('_dispose');
    },
});
