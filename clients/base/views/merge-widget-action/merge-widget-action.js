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
 * @class View.Views.Base.MergeWidgetActionView
 * @alias SUGAR.App.view.views.BaseMergeWidgetActionView
 * @extends View.View
 */
({
    /**
    * @property {string}
    */
    tagName: 'span',

    /**
     * The layout for the widget
     *
     * @property {Layout}
     */
    _documentMergeWidgetLayout: null,

    events: {
        'click [data-action=merges]': 'toggleDocumentMergeWidget'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this._isVisible = false;

        app.events.on('document_merge:show_widget', this.showWidget, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.tooltipPlacement = app.lang.direction === 'ltr' ? 'right' : 'left';
        this._super('_render');
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.isAvailable = this._isAvailable();

        this._super('_renderHtml');
    },

    /**
     * Util to determine if Document Merging is available for this instance
     *
     * @return {boolean} True if Document Merging should be available
     * @private
     */
    _isAvailable: function() {
        return app.api.isAuthenticated() && app.user.isSetupCompleted();
    },

    /**
     * Displays the widget
     */
    showWidget: function() {
        if (this.isVisible() === false) {
            this.toggleDocumentMergeWidget();
        }

        this._documentMergeWidgetLayout.reload();
    },

    /**
     * Initializes the widget layout
     */
    _initializeWidgetLayout: function() {
        if (!this._documentMergeWidgetLayout || this._documentMergeWidgetLayout.disposed === true) {
            this._createDocumentMergeWidgetLayout();
        }
    },

    /**
     * Toggles the display of the widget
     *
     * @param {Event} e
     */
    toggleDocumentMergeWidget: function(e) {
        if (!_.isUndefined(e) && e.originalEvent instanceof Event) {
            e.stopPropagation();
            e.preventDefault();
        }

        if (!app.isSynced) {
            return;
        }

        if (this.$el.hasClass('disabled')) {
            return;
        }

        this._initializeWidgetLayout();

        this._documentMergeWidgetLayout.toggle();
    },

    /**
     * Creates the document merge widget layout.
     *
     * @private
     */
    _createDocumentMergeWidgetLayout: function() {
        this._documentMergeWidgetLayout = app.view.createLayout({
            module: 'DocumentMerges',
            type: 'merge-widget',
            button: this.$el
        });
        this._documentMergeWidgetLayout.initComponents();
        this._documentMergeWidgetLayout._initPopover(this.$el);

        this.listenTo(this._documentMergeWidgetLayout, 'show hide', function(view, active) {
            this.$el.toggleClass('active', active);
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this._documentMergeWidgetLayout) {
            this._documentMergeWidgetLayout.dispose();
        }

        this._super('_dispose');
    }
});
