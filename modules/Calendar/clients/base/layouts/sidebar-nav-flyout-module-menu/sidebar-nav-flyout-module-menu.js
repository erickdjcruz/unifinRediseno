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
 * @class View.Layouts.Base.CalendarSidebarNavFlyoutModuleMenuLayout
 * @alias SUGAR.App.view.layouts.BaseCalendarSidebarNavFlyoutModuleMenuLayout
 * @extends View.Layout
 */
({
    extendsFrom: 'SidebarNavFlyoutModuleMenuLayout',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.listenTo(this.layout, 'popover:opened', this._populateCalendarModules);
    },

    /**
     * Adds the calendar modules to the menu components.
     *
     * @private
     */
    _getMenuComponents: function() {
        let menuComponents = this._super('_getMenuComponents');
        menuComponents.splice(1,0 ,{
            view: {
                type: 'sidebar-nav-flyout-actions',
                name: 'calendar-modules',
                actions: []
            }
        });
        return menuComponents;
    },

    /**
     * Calls the the Calendar Modules api to get the list of calendar modules.
     *
     * @private
     */
    _populateCalendarModules: function() {
        app.api.call('read', app.api.buildURL('Calendar/modules'), {}, {
            success: _.bind(this._populateCalendarModulesSucceess, this)
        });
    },

    /**
     * Populates the calendar modules componenet and renders the actions.
     * @param data
     * @private
     */
    _populateCalendarModulesSucceess: function(data) {
        let actions = [];
        let calendarModules = this.getComponent('calendar-modules');

        _.each(data.modules, function(moduleInfo, module) {
            let createLabel = '';
            if (module === 'KBContents') {
                createLabel = app.lang.getModString('LNK_NEW_KBCONTENT_TEMPLATE', module);
            } else {
                let createLabelKey = 'LNK_NEW_' + moduleInfo.objName.toUpperCase();
                createLabel = app.lang.get(createLabelKey, module);

                if (createLabel === createLabelKey) {
                    createLabelKey = 'LNK_NEW_RECORD';
                    createLabel = app.lang.getModString(createLabelKey, module);
                }
            }
            actions.push({
                acl_action: 'create',
                acl_module: module,
                icon: app.metadata.getModule(module).icon || '',
                label: createLabel,
                route: `#${module}/create`
            });
        }, this);
        actions.push({
            type: 'divider'
        });
        calendarModules.updateActions(actions);
    }
})
