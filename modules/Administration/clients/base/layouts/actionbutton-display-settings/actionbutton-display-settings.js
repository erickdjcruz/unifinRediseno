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
 * Action button tab view
 *
 * @class View.Layouts.Base.AdministrationActionbuttonDisplaySettingsLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationActionbuttonDisplaySettingsLayout
 * @extends View.Layout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initial setup of properties
     */
    _initProperties: function() {
        this._sideView = null;
        this._sidePreview = null;
    },

    /**
     * Context model event registration
     */
    _registerEvents: function() {
        var ctxModel = this.context.get('model');

        this.listenTo(ctxModel, 'update:side-pane:view', this.changeView, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._createSideView('record');
        this._createSidePreview('record');
    },

    /**
     * When the tab is changed, we need to change the view
     * @param {string} viewID
     */
    changeView: function(viewID) {
        this._createSidePreview(viewID);
        this._createSideView(viewID);
    },

    /**
     * Create the side view
     * @param {string} viewID
     */
    _createSideView: function(viewID) {
        this._disposeSideView();
        var svContainer = this.$('[data-container="ab-admin-side-container"]');

        svContainer.empty();

        this._sideView = app.view.createView({
            name: 'actionbutton-display-settings-' + viewID,
            context: this.context,
            model: this.context.get('model'),
            layout: this,
        });

        this._sideView.render();

        svContainer.append(this._sideView.$el);
    },

    /**
     * Create the side view
     * @param {string} viewID
     */
    _createSidePreview: function(viewID) {
        this._disposeSidePreview();
        var svContainer = this.$('[data-container="ab-admin-side-preview"]');

        svContainer.empty();

        this._sidePreview = app.view.createView({
            name: 'actionbutton-preview-' + viewID,
            context: this.context,
            model: this.context.get('model'),
            layout: this,
        });

        this._sidePreview.render();

        svContainer.append(this._sidePreview.$el);
    },

    /**
     * Dispose side pane view
     */
    _disposeSideView: function() {
        if (this._sideView) {
            this._sideView.dispose();

            this._sideView = null;
        }
    },

    /**
     * Dispose side pane preview
     */
    _disposeSidePreview: function() {
        if (this._sidePreview) {
            this._sidePreview.dispose();

            this._sidePreview = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeSideView();
        this._disposeSidePreview();

        this._super('_dispose');
    },
})
