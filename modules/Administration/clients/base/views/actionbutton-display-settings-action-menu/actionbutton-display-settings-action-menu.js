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
 * Action button configuration settings view
 *
 * @class View.Views.Base.AdministrationActionbuttonDisplaySettingsActionMenuView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonDisplaySettingsActionMenuView
 * @extends View.View
 */
({
    events: {
        'change [data-fieldname=orderNumber]': 'orderNumberChanged',
        'change [data-fieldname=listView]': 'changeListView',
        'change [data-fieldname=recordView]': 'changeRecordView',
        'change [data-fieldname=recordViewDashlet]': 'changeRecordViewDashlet',
        'change [data-fieldname=subpanels]': 'changeSubpanels',
        'change [data-fieldname=focusDashboardHeader]': 'changeSetting',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._updateActionMenuSettings();
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        const model = options.context.get('model');

        this._actionMenu = model.get('data').actionMenu;
        this._orderData = [];

        let actionViewButtonsNo = 0;

        const moduleMeta = app.metadata.getModule(model.get('module'));

        if (moduleMeta) {
            _.each(moduleMeta.fields, function getButton(button) {
                if (button.type === 'actionbutton') {
                    this._orderData.push(this._orderData.length + 1);

                    if (!_.isEmpty(JSON.parse(button.options).actionMenu)) {
                        actionViewButtonsNo = actionViewButtonsNo + 1;
                    }
                }
            }, this);
        }

        if (_.isEmpty(this._actionMenu)) {
            this._actionMenu = {
                orderNumber: actionViewButtonsNo + 1,
                listView: false,
                recordView: false,
                recordViewDashlet: false,
                focusDashboardHeader: false,
                subpanels: false
            };
        }

        if (actionViewButtonsNo === 0) {
            this._orderData.push(this._orderData.length + 1);
        }
    },

    /**
     * Updates configuration and re-renders preview
     *
     */
    _updateActionMenuSettings: function() {
        var ctxModel = this.context.get('model');
        var buttonsData = ctxModel.get('data');
        buttonsData.actionMenu = this._actionMenu;

        this.context.trigger('update-buttons-preview', buttonsData);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this.$('.ab-admin-order').select2();
    },

    /**
     * Handle order number change event
     *
     * @param {UIEvent} e
     */
    orderNumberChanged: function(e) {
        this._actionMenu.orderNumber = e.currentTarget.value;

        this._updateActionMenuSettings();
    },

    /**
     * Handle list view visibility property
     *
     * @param {UIEvent} e
     */
    changeListView: function(e) {
        this._actionMenu.listView = e.currentTarget.checked;

        this._updateActionMenuSettings();
    },

    /**
     * Update record view visibility property
     *
     * @param {UIEvent} e
     */
    changeRecordView: function(e) {
        this._actionMenu.recordView = e.currentTarget.checked;

        this._updateActionMenuSettings();
    },

    /**
     * Update record view dashlet visibility property
     *
     * @param {UIEvent} e
     */
    changeRecordViewDashlet: function(e) {
        this._actionMenu.recordViewDashlet = e.currentTarget.checked;

        this._updateActionMenuSettings();
    },

    /**
     * Update setting.
     *
     * @param {UIEvent} e
     */
    changeSetting: function(e) {
        let settingName = $(e.currentTarget).data('fieldname');
        this._actionMenu[settingName] = e.currentTarget.checked;

        this._updateActionMenuSettings();
    },

    /**
     * Update subpanels visibility property
     *
     * @param {UIEvent} e
     */
    changeSubpanels: function(e) {
        this._actionMenu.subpanels = e.currentTarget.checked;

        this._updateActionMenuSettings();
    },
});
