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
 * @class View.Views.Base.OpportunitiesConfigHeaderButtonsView
 * @alias SUGAR.App.view.views.BaseOpportunitiesConfigHeaderButtonsView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    extendsFrom: 'ConfigHeaderButtonsView',

    render: function(options) {
        this._super('render', options);
        this.enableButton(false);
    },

    /**
     * @inheritdoc
     * @param {*} onClose
     */
    showSavedConfirmation: function(onClose) {
        app.alert.dismiss('opp.config.save');
        this._super('showSavedConfirmation', [onClose]);
    },

    /**
     * Displays confirm alert
     */
    displayWarningAlertDenormFramework: function() {
        var message = app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_SAVE_WARNING', 'Administration');
        app.alert.show('relate-denormalization-warning', {
            level: 'confirmation',
            title: app.lang.get('LBL_WARNING'),
            messages: message,
            onConfirm: _.bind(function() {
                this.context.trigger('relate-denormalization:save');
            }, this)
        });
    },

    /**
     * Overriding the default saveConfig to display the warning alert first, then on confirm of the
     * warning alert, save the config.
     *
     * @inheritdoc
     */
    saveConfig: function() {
        this.displayWarningAlertDenormFramework();
    },

    enableButton: function(flag) {
        this.getField('save_button').setDisabled(!flag);
    }
})
