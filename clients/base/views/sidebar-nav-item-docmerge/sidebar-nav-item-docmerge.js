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
 * @class View.Views.Base.SidebarNavItemDocmergeView
 * @alias SUGAR.App.view.views.BaseSidebarNavItemDocmergeView
 * @extends View.Views.Base.SidebarNavItemView
 */
({
    extendsFrom: 'SidebarNavItemView',

    initialize: function(options) {
        options.meta = options.meta || {};
        options.meta.template = options.meta.template || 'sidebar-nav-item';
        this.plugins = _.union(this.plugins || [], ['DocumentMerge']);
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this.hasAccess() ? this.$el.show() : this.$el.hide();
    },

    /**
     * Opens the flyout on primary click
     *
     * @override
     */
    primaryActionOnClick: function(event) {
        this.secondaryActionOnClick(event);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.listenTo(app.events, 'document_merge:show_widget', this.showWidget);
    },

    /**
     * Opens the doc merge flyout if it is closed, and triggers its contents to
     * refresh
     */
    showWidget: function() {
        if (!this.flyout) {
            this.secondaryActionOnClick();
        } else {
            this.flyout.open();
        }

        this.flyout.trigger('reload');
    },

    /**
     * @inheritdoc
     *
     * Forces the view to use the doc merge widget for its flyout
     */
    _getFlyoutComponents: function() {
        return [
            {
                layout: 'sidebar-merge-widget',
                loadModule: 'DocumentMerges'
            }
        ];
    },

    /**
     * @inheritdoc
     */
    dispose: function() {
        this.stopListening();
        this._super('dispose');
    }
})
