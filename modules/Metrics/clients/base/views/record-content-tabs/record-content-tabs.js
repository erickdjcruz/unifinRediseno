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
 * @class View.Views.Base.Metrics.RecordContentTabsView
 * @alias SUGAR.App.view.views.BaseMetricsRecordContentTabsView
 * @extends View.Views.Base.ConfigPanelView
 */
({
    extendsFrom: 'BaseConfigPanelView',

    activeTabIndex: 0,

    events: {
        'click .record-panel .btn-toggle': 'togglePanel',
    },

    /**
     * @inheritdoc
     */
    render: function() {
        var self = this;
        this.action = this.context.get('action');
        this._super('render');

        this.toggleFreezeColumn();

        this.$('#tabs').tabs({
            active: this.context.get('activeTabIndex') || 0,
            classes: {
                'ui-tabs-active': 'active',
            },

            // when selecting another tab, show/hide the corresponding side [ane div accordingly
            activate: function(event, ui) {
                let paneGroup = $('.record-side-pane-group');
                let ariaControls = $(event.currentTarget).closest('li').attr('aria-controls');

                paneGroup.toggle(self.action === 'edit' && ariaControls === 'list_layout');
                $('.sidebar-toggle', this.$el).toggle(ariaControls !== 'settings');
            }
        });
    },

    /**
     * Show/hide the Freeze first column config for the user based on the admin settings
     */
    toggleFreezeColumn: function() {
        if (!app.config.allowFreezeFirstColumn) {
            let freezeElem = this.$('.freeze-config') || {};
            let freezeCell =
                freezeElem.length > 0 && freezeElem.closest('.row-fluid') ? freezeElem.closest('.row-fluid') : {};
            if (freezeCell.length > 0) {
                let freezeCellIndex = freezeCell.index();
                let configParentElem = freezeCell.parent() || {};
                // get the header label element for freeze option
                let fieldHeader = configParentElem.length > 0 && configParentElem.children() ?
                    configParentElem.children().eq(freezeCellIndex - 1) : {};
                fieldHeader.hide();
                freezeCell.hide();
            }
        }
    },

    /**
     * Hide or show panel based on click to the panel toggle button
     * @param {Event} evt
     */
    togglePanel: function(evt) {
        this.$(evt.currentTarget)
            .closest('.record-panel')
            .toggleClass('folded');
    },
})
