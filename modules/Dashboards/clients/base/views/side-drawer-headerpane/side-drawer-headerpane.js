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
 * @class View.Views.Dashboards.SideDrawerHeaderpaneView
 * @alias SUGAR.App.view.views.DashboardsSideDrawerHeaderpaneView
 * @extends View.Views.Dashboards.DashboardHeaderpaneView
 */
({
    /**
     * This is a special header for side drawers that contain a dashlet.
     */
    extendsFrom: 'DashboardsDashboardHeaderpaneView',

    events: {
        'mousemove .record-edit-link-wrapper, .record-lock-link-wrapper': 'handleMouseMove',
        'mouseleave .record-edit-link-wrapper, .record-lock-link-wrapper': 'handleMouseLeave',
        'click [name=edit_button]': 'editClicked',
        'click [name=save_button]': 'saveClicked',
        'click [name=cancel_button]': 'cancelClicked',
        'click [name=create_cancel_button]': 'createCancelClicked',
        'click [name=edit_overview_tab_button]': 'editOverviewTabClicked'
    },

    /**
     * @inheritdoc
     */
    _setMaxWidthForEllipsifiedCell: function($ellipsisCell, width) {
        $ellipsisCell.css({'max-width': width});
    },

    /**
     * Adjusts dropdown menu position.
     */
    adjustDropdownMenu: function() {
        let $title = this.$('.record-cell');
        let $menu = this.$('.dropdown-menu');
        // dropdown toggle is 28px wide
        // dropdown menu is shown to the right of the toggle by default
        if (($title.outerWidth() - 28 + $menu.width()) > this._containerWidth) {
            if ($menu.width() < $title.width()) {
                // show dropdown menu to the left of the toggle
                $menu.css({right: 0, left: 'auto'});
            } else {
                let maxWidth = this._containerWidth - $title.outerWidth() + 28;
                $menu.css({'max-width': maxWidth});
            }
        } else {
            $menu.removeAttr('style');
        }
    },

    /**
     * @override
     */
    bindDataChange: function() {
        if (!this.model) {
            return;
        }

        this.model.on('change', this._updateTabTitle, this);
        this.context.on('side-drawer-headerpane:empty-tab-title', this._setEmptyTabTitle, this);
        this.layout.on('headerpane:adjust_fields', this.adjustDropdownMenu, this);
        if (!_.isEmpty(this.context.parent) && !_.isEmpty(this.context.parent.parent)) {
            let rowModel = this.context.parent.parent.get('rowModel');
            if (rowModel) {
                this.listenTo(rowModel, 'change', this._updateTabContent);
            }
        }
    },

    /**
     * Refresh tab content.
     */
    _updateTabContent: function() {
        app.alert.dismiss('data:sync:success');
        this.render();
        app.sideDrawer.refreshTab();
    },

    /**
     * Update dashboard and record name for active tab.
     */
    _updateTabTitle: function() {
        let activeTab = app.sideDrawer.getActiveTab();
        if (activeTab && activeTab.isFocusDashboard && !activeTab.hasTitle) {
            activeTab.dashboardName = app.lang.get(this.model.get('name'), activeTab.context.module);
            if (!activeTab.recordName && activeTab.context.model) {
                activeTab.recordName = activeTab.context.model.get('name');
            }
            if (!activeTab.context.dataTitle) {
                let moduleMeta = app.metadata.getModule(activeTab.context.module);
                let labelColor = (moduleMeta) ? `label-module-color-${moduleMeta.color}` :
                                                `label-${activeTab.context.module}`;
                activeTab.context.dataTitle = {
                    module: app.lang.get('LBL_MODULE_NAME_SINGULAR', activeTab.context.module),
                    view: app.lang.get('LBL_RECORD'),
                    name: activeTab.recordName,
                    labelColor: labelColor
                };
            }
            activeTab.hasTitle = true;
            app.sideDrawer.renderTabs();
        }
    },

    /**
     * Set default title name if tab is empty
     */
    _setEmptyTabTitle: function() {
        let activeTab = app.sideDrawer.getActiveTab();
        if (activeTab && activeTab.context && activeTab.context.module) {
            let moduleMeta = app.metadata.getModule(activeTab.context.module);
            let labelColor = (moduleMeta) ? `label-module-color-${moduleMeta.color}` :
                                            `label-${activeTab.context.module}`;
            activeTab.dashboardName = app.lang.get('LBL_NO_DASHBOARD_CONFIGURED');
            activeTab.context.dataTitle = {
                module: app.lang.get('LBL_MODULE_NAME_SINGULAR', activeTab.context.module),
                view: app.lang.get('LBL_RECORD'),
                name: activeTab.recordName,
                labelColor: labelColor
            };
            activeTab.hasTitle = true;
            app.sideDrawer.renderTabs();
        }

    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (this.context.get('create') && !this.context.get('emptyDashboard')) {
            this.createView = true;
            this.action = 'edit';
        } else {
            this.createView = false;
            this.dashboardTitle = !this.context.get('emptyDashboard') && app.sideDrawer.getActiveTab() &&
                app.sideDrawer.getActiveTab().isFocusDashboard;
            this.action = 'view';
        }
        this._super('_render');
    },

    /**
     * @inheritdoc
     */
    unbind: function() {
        this._super('unbind');
        this.layout.off('headerpane:adjust_fields', this.adjustDropdownMenu);
    }
})
