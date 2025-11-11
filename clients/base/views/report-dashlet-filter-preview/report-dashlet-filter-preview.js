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
 * @class View.Views.Base.ReportDashletFilterPreviewView
 * @alias SUGAR.App.view.views.BaseReportDashletFilterPreviewView
 * @extends View.View
 */
 ({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this._dashletTitle = this.model.get('label');
        this._previewController = false;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._createPreviewController();
    },

    /**
     * Refresh the UI
     */
    refreshPreview: function() {
        this._dashletTitle = this.model.get('label');
        this.render();
    },

    /**
     * Create the preview controller
     */
    _createPreviewController: function() {
        this._disposePreviewController();

        this._previewController = app.view.createView({
            type: 'report-dashlet-filter',
            context: this.context,
            model: this.model,
            stayCollapsed: true,
            hideToolbar: true,
            usePreviewClasses: true,
        });

        this._previewController.render();
        this.$('[data-container="previewBody"]').append(this._previewController.$el);
    },

    /**
     * Dispose the preview controller
     */
    _disposePreviewController: function() {
        if (this._previewController) {
            this._previewController.dispose();
            this._previewController = false;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposePreviewController();

        this._super('_dispose');
    },
})
