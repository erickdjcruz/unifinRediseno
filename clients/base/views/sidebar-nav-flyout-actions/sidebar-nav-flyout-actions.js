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
 * @class View.Views.Base.SidebarNavFlyoutMenuView
 * @alias SUGAR.App.view.views.BaseSidebarNavFlyoutMenuView
 * @extends View.View
 */
({
    events: {
        'click [data-event]': '_handleEventItemClick',
        'click [data-route]': '_handleRouteItemClick'
    },

    /**
     * Stores the list of menu items to render in the view
     */
    actions: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.updateActions(this.meta && this.meta.actions || []);
    },

    /**
     * Updates and re-renders the list of actions to show in the menu
     *
     * @param actionMeta
     */
    updateActions: function(actionMeta) {
        this.actions = this._filterActionsByAccess(actionMeta);
        this.render();
    },

    /**
     * Filters menu actions by ACLs for the current user.
     *
     * @param {Array} actionMeta The menu action metadata to check for access
     * @return {Array} the list of actions for which the user has access
     */
    _filterActionsByAccess: function(actionMeta) {
        let result = [];
        _.each(actionMeta, function(menuItem) {
            if (app.acl.hasAccess(menuItem.acl_action, menuItem.acl_module)) {
                result.push(menuItem);
            }
        });
        return result;
    },

    /**
     * Handles when an event-based action item in the menu is clicked
     *
     * @param {Event} event The event that triggered this (normally a click
     *   event).
     */
    _handleEventItemClick: function(event) {
        let target = this.$(event.currentTarget);
        let eventTarget = app.events;
        if (target.data('event-target') === 'layout') {
            eventTarget = this.layout;
        }
        eventTarget.trigger(target.data('event'), this.module, event);
    },

    /**
     * Handles when a route-based action item in the menu is clicked
     *
     * Since we normally trigger the drawer for some actions, we prevent it
     * when using the click with the `ctrlKey` (or `metaKey` in Mac OS).
     * We also prevent the routing to be fired when this happens.
     *
     * When we are triggering the same route that we already are in, we just
     * trigger a {@link Core.Routing#refresh}.
     *
     * @param {Event} event The event that triggered this (normally a click
     *   event).
     */
    _handleRouteItemClick: function(event) {
        let target = this.$(event.currentTarget);
        let route = target.data('route');

        event.preventDefault();
        if (
            (!_.isUndefined(event.button) && event.button !== 0) ||
            event.ctrlKey ||
            event.metaKey ||
            target.data('openwindow') === true
        ) {
            event.stopPropagation();
            window.open(route, '_blank');
            return false;
        }

        let currentRoute = `#${app.router.getFragment()}`;
        if (currentRoute === route) {
            app.router.refresh();
        } else {
            app.router.navigate(route, {trigger: true});
        }
    }
})
