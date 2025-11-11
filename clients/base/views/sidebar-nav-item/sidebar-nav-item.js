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
 * @class View.Views.Base.SidebarNavItemView
 * @alias SUGAR.App.view.views.BaseSidebarNavItemView
 * @extends View.View
 */
({
    className: `sidebar-nav-item transition-transform ease-in-out duration-300 relative bg-[--sidebar-nav-background]
hover:bg-[--sidebar-nav-background-hover] h-10 min-h-[2.5rem] group/nav-item`,

    /**
     * Set id property on the element
     * @return {string}
     */
    id: function() {
        if (this.options.meta && this.options.meta.name) {
            return `${this.options.meta.name}_sidebar-nav-item`;
        }
    },

    events: {
        'click .sidebar-nav-item-btn': 'primaryActionOnClick',
        'click .sidebar-nav-item-kebab': 'secondaryActionOnClick'
    },

    /**
     * Stores the string label for this nav item
     */
    label: '',

    /**
     * Stores the string route for this nav item if applicable
     */
    route: '',

    /**
     * Stores the string name of the icon used for this nav item
     */
    icon: '',

    /**
     * Stores the boolean determining whether the secondary action button
     * should be displayed
     */
    secondaryAction: false,

    /**
     * Stores the flyout layout object
     */
    flyout: null,

    /**
     * Stores whether the nav item is currently active
     */
    active: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initConfig();
    },

    /**
     * Sets properties on the nav item based on options passed in
     *
     * @private
     */
    _initConfig: function() {
        let viewMeta = this.meta || {};
        this.label = viewMeta.label || '';
        this.route = viewMeta.route ? `#${this.meta.route}` : '';
        this.event = viewMeta.event || '';
        this.icon = viewMeta.icon || '';
        this.secondaryAction = viewMeta.secondary_action || false;
        this.track = viewMeta.track;
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(app.events, 'app:sync:complete', this._handleAppSyncComplete);
    },

    /**
     * Sets the active status of the nav item
     * @param active
     */
    setActive: function(active) {
        this.active = !!active;
        this.$el.toggleClass('bg-[--sidebar-nav-background]', !active);
        this.$el.toggleClass('active bg-[--sidebar-nav-background-hover]', !!active);
    },

    /**
     * Handles when the app has finished syncing. By default, this simply
     * triggers a render so that the nav item will show, but can be
     * overridden by other nav item types if needed
     *
     * @private
     */
    _handleAppSyncComplete: function() {
        this._initConfig();
        this.render();
    },

    /**
     * @inheritdoc
     *
     * Tracks whether the app is synced before rendering, which is necessary
     * in order to use the app router, label language metadata, etc.
     */
    _render: function() {
        // If we re-render, the existing flyout instance won't be attached to
        // the new DOM element for the sidebar-nav-item, so it will need to be re-created
        if (this.flyout) {
            this.stopListening(this.flyout);
            this.flyout.dispose();
            this.flyout = null;
        }

        this.tooltipPlacement = app.lang.direction === 'ltr' ? 'right' : 'left';
        this.appIsSynced = app.isSynced;
        if (this.secondaryAction) {
            this.secondaryActionLabel = app.lang.get('LBL_SIDEBAR_NAV_ITEM_MENU', this.module, {
                subject: app.lang.get(this.label, this.module, this)
            }, this);
        }
        this._super('_render');
    },

    /**
     * Handles the click event on the sidebar nav item's primary click element.
     * By default, routes the app to the route defined in metadata
     *
     * @param {Event} event the primary action click event
     */
    primaryActionOnClick: function(event) {
        event.preventDefault();
        if (this.route) {
            let $currentTarget = this.$(event.currentTarget);
            if ((!_.isUndefined(event.button) && event.button !== 0) ||
                event.ctrlKey ||
                event.metaKey ||
                $currentTarget.data('openwindow') === true) {
                event.stopPropagation();
                window.open(this.route, '_blank');
                return false;
            }
            let currentRoute = `#${app.router.getFragment()}`;
            if (currentRoute === this.route) {
                app.router.refresh();
            } else {
                app.router.navigate(this.route, {trigger: true});
            }
        } else if (this.event) {
            app.events.trigger(this.event, event);
        }
        this.$el.find('.sidebar-nav-item-btn').blur();
    },

    /**
     * Handles the click event on the secondary button
     */
    secondaryActionOnClick: function() {
        if (!this.flyout) {
            this.initPopover(this.$el, this._getFlyoutComponents(), this._getShowClose());
        }
        this.flyout.toggle();
    },

    /**
     * Sets the active status of this nav item automatically
     *
     * @private
     */
    _determineActiveStatus: function() {
        this.setActive(this.flyout && this.flyout.isOpen);
    },

    /**
     * Returns the metadata for the components to place in the flyout
     * container. Uses the flyoutComponents defined in metadata by default, but
     * can be overridden in custom nav item types
     *
     * @return {Array} The flyout component metadata definition
     * @private
     */
    _getFlyoutComponents: function() {
        return this.meta && this.meta.flyoutComponents || [];
    },

    /**
     * Returns the metadata which determines
     * the flyout layout.
     * @return {boolean} if
     * @private
     */
    _getShowClose: function() {
        if (this.meta && !_.isUndefined(this.meta.showClose)) {
            return this.meta.showClose;
        }
        return true;
    },

    /**
     * Adds the popover to the the sidebar-nav-item
     * @param anchor The element the popover is attached to.
     * @param {Array} components An array of componenets to be places in the
     *                          popover.
     */
    initPopover: function(anchor, components, showClose = false) {
        let templateTpl = app.template.getLayout('sidebar-nav-flyout.template');
        this.flyout = app.view.createLayout({
            type: 'sidebar-nav-flyout',
            anchor: anchor,
            showClose: showClose,
            meta: {
                'components': components
            },
            module: this.module
        });

        this.flyout.initComponents();
        this.flyout.render();

        this.listenTo(this.flyout, 'popover:opened popover:closed', this._determineActiveStatus);

        const placement = app.lang.direction === 'rtl' ? 'left' : 'right';
        anchor.popover({
            html: true,
            trigger: 'manual',
            //This works for but may need to be changed as elements start moving
            //during resizes.
            template: templateTpl(),
            animation: false,
            placement: placement,
            fallbackPlacements: [placement],
            content: _.bind(function() {
                return this.flyout.$el;
            }, this),
            offset: [0, 0],
        });
    },

    /**
     * @inheritdoc
     */
    dispose: function() {
        this.stopListening();
        this._super('dispose');
    }
})
