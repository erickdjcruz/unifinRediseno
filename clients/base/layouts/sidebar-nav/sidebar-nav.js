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
 * @class View.Layouts.Base.SidebarNavLayout
 * @alias SUGAR.App.view.layouts.BaseSidebarNavLayout
 * @extends View.Layout
 */
({
    events: {
        'click .sidebar-nav-overlay': '_toggleExpand'
    },

    /**
     * Stores a boolean determining whether the sidebar is currently in expanded mode
     */
    expanded: false,

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(app.events, 'app:login', this.hide);
        this.listenTo(app.events, 'app:login:success', this.show);
        this.listenTo(app.events, 'sidebar-nav:expand:toggle', this._toggleExpand);

        this.listenTo(app.events, 'app:sync:complete', function() {
            this.stopListening(app.router, 'route', this.collapse);
            this.listenTo(app.router, 'route', this.collapse);
        });
    },

    /**
     * Collapses the navigation sidebar into compact mode
     */
    collapse: function() {
        if (this.expanded) {
            this._toggleExpand(false);
        }
    },

    /**
     * Toggles the sidebar nav between expanded/collapsed modes
     *
     * @param {bool} expand optional, true to force expanded mode and false to
     *                      force collapsed mode
     * @private
     */
    _toggleExpand: function(expand) {
        this.expanded = _.isBoolean(expand) ? expand : !this.expanded;

        // Mark/unmark the the sidebar element as expanded. Add a temporary
        // class to assist with any animation styling
        let sidebarEl = this.$el.find('.sidebar-nav');
        let animationClass = this.expanded ? 'expanding' : 'collapsing';
        sidebarEl.addClass(animationClass);
        setTimeout(() => {
            sidebarEl.toggleClass('expanded', this.expanded);
            sidebarEl.removeClass(animationClass);
        }, 300);

        // Show/hide the expanded mode overlay
        let overlayEl = this.$el.find('.sidebar-nav-overlay');
        overlayEl.toggleClass('hide', !this.expanded);

        app.events.trigger('sidebar-nav:expand:toggled', this.expanded);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // If we are authenticated show the navbar
        if (this._isAvailable()) {
            this.show();
        } else {
            this.hide();
        }
    },

    /**
     * Need to hide the sidebar nav on cookie consent and setup
     *
     * @return {boolean} True if Sidebar Nav should be available
     * @private
     */
    _isAvailable: function() {
        return app.api.isAuthenticated() &&
            (app.user.isSetupCompleted() || app.cache.has('ImpersonationFor'));
    },

    /**
     * Places all components within this layout inside sidebar-nav div
     * @param component
     * @private
     */
    _placeComponent: function(component) {
        this.$el.find('.sidebar-nav').append(component.$el);
    },

    /**
     * @inheritdoc
     */
    dispose: function() {
        this.stopListening();
        this._super('dispose');
    }
})
