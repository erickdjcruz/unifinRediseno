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
 * Parent field view selection
 *
 * @class View.Views.Base.AdministrationActionbuttonPreviewActionMenuView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonPreviewActionMenuView
 * @extends SUGAR.App.view.views.BaseAdministrationActionbuttonPreviewRecordView
 */
({
    extendsFrom: 'AdministrationActionbuttonPreviewRecordView',

    /**
     * @inheritdoc
     */
    _getPreparedButtonsData: function(buttonsData) {
        let data = this._super('_getPreparedButtonsData', [buttonsData]);

        data.settings.type = 'dropdown';

        _.each(data.buttons, function increaseOrderNumber(buttonData) {
            buttonData.orderNumber = buttonData.orderNumber + 1;
        });

        const dropdownButtonData = {
            actions: {},
            active: true,
            buttonId: app.utils.generateUUID(),
            orderNumber: 0,
            properties: {
                label: 'Edit',
                colorScheme: 'primary',
                showLabel: true,
                showIcon: false,
            },
        };

        data.buttons[dropdownButtonData.buttonId] = dropdownButtonData;

        return data;
    },
});
