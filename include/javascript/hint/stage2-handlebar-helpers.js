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
 * Handlebars helpers.
 *
 * These functions are to be used in handlebars templates.
 *
 * @class Handlebars.helpers
 * @singleton
 */
(function(app) {
    app.events.on('app:init', function() {

        Handlebars.registerHelper('toUpperCase', function(str) {
            if (!str) {
                return str;
            }
            return str.toUpperCase();
        });

        // greater than
        Handlebars.registerHelper('hint_gt', function(a, b) {
            var next = arguments[arguments.length - 1];
            return (a > b) ? next.fn(this) : next.inverse(this);
        });

        Handlebars.registerHelper('hint_safeUrl', function(url) {
            if (url) {
                if (!url.match(/^([a-zA-Z]+):/)) {
                    url = 'http://' + url;
                }
                var whiteList = app.config.allowedLinkSchemes;
                var filteredWhitelist = whiteList.filter(function(scheme) {
                    return url.toLowerCase().indexOf(scheme + '://') === 0;
                });

                // did not start with https?://, so turn the url into something safe
                if (!filteredWhitelist.length) {
                    url = '#';
                }
            }
            return encodeURI(url);
        });

        // helper to determine if Hint should enable Sugar 10.2 flexible dashboards
        Handlebars.registerHelper('flexibleDashboards', function() {
            // versionCompare('10.2.0') will return -1 if the current Sugar version
            // is earlier than 10.2. We only want to allow flexible dashboards iff
            // the current Sugar version is at least 10.2
            return app.hint.versionCompare('10.2.0') > -1;
        });

    });

    /**
     * Lookup a specific layout recursively. Recommended to call it with
     * app.controller.layout._components initially.
     *
     * @param {string} module Current module
     * @param {string} view Current view
     * @param {null|Object} layout The target layout
     * @param {Array} components List of child components
     */
    function getActiveLayout(module, view, layout, components) {
        _.each(components, function(cmp) {
            if (cmp.module === module && cmp.type === view) {
                layout = cmp;
            }
        });

        if (!layout) {
            _.each(components, function(cmp) {
                var shouldSearchDeeper = !layout && cmp._components && cmp._components.length > 0;
                if (shouldSearchDeeper) {
                    layout = getActiveLayout(module, view, layout, cmp._components);
                }
            });
        }

        return layout;
    }

    /**
     * Set account name change listener
     *
     * @param {string} module
     * @param {string} view
     */
    function setAccountNameChangeListener(module, view) {
        if (module === 'Contacts' && view === 'create' && !_inDrawer(module)) {
            var createView;
            var createLayout = app.drawer.getComponent('create');

            if (!createLayout) { // Create layout without an actual drawer
                var mainComponents = app.controller.layout._components;
                createLayout = getActiveLayout(module, view, null, mainComponents);
                createView = createLayout.layout;
            } else { // We have a standard create drawer
                createView = createLayout.getComponent('sidebar').getComponent('main-pane');
            }

            createLayout.model.on('change:account_name', function() {
                var hintModel = _.first(createView.collection.models);
                app.events.trigger('preview:close');
                app.events.trigger('preview:render', hintModel);
                app.events.trigger('hint:user-input', true);
            });
        }
    }

    /**
     * Detect if we're actually in a drawer that's been opened from one of
     * our Hint-enhanced modules instead of in the top level module UI
     *
     * @param expectedModule  module name of the main opened modules
     * @return {boolean} true if running in a drawer, false otherwise.
     * @private
     */
    function _inDrawer(expectedModule) {
        // REMIND: this is probably not the best way to detect this
        var comps = app.additionalComponents;
        if (comps && comps.drawer && comps.drawer._components &&
            comps.drawer._components.length > 0) {
            // just being in this context is probably sufficient, but it's better to make sure
            // this is not an accidental situation
            const drawerComp = comps.drawer._components[0];
            return drawerComp.module != expectedModule;
        }

        return false;
    }

    app.events.on('app:view:change', function(view) {
        var _module = app.controller.context.get('module');

        setAccountNameChangeListener(_module, view);
    });
})(SUGAR.App);
