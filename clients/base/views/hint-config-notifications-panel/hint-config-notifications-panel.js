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
 * @class View.Views.Base.HintConfigNotificationsPanelView
 * @alias SUGAR.App.view.views.BaseHintConfigPanelView
 * @extends View.Views.Base.ConfigPanelView
 */
({
    plugins: ['Stage2CssLoader'],

    extendsFrom: 'ConfigPanelView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        var url = app.api.buildURL('get/hint/configNotificationObject');
        app.api.call('read', url, null, {
            success: function(config) {
                // if no entry in table or the entry is set to disabled notifications
                if (_.isEmpty(config) || !config) {
                    $('#config_enable_hint_notifications').prop('checked',true);
                } else {
                    $('#config_disable_hint_notifications').prop('checked',true);
                }
            },
            error: function(err) {
                console.log('Error fetching configuration', err);
            }
        });

        this.name = app.lang.get('LBL_HINT_CONFIG_NOTIFICATIONS_HEADER');
        this.services = [
            {
                title: app.lang.get('LBL_HINT_CONFIG_ENABLE_NOTIFICATIONS'),
                name: 'config_hint_notifications',
                id: 'config_enable_hint_notifications',
                showWarning: false
            },
            {
                title: app.lang.get('LBL_HINT_CONFIG_DISABLE_NOTIFICATIONS'),
                name: 'config_hint_notifications',
                id: 'config_disable_hint_notifications',
                showWarning: false
            },
        ];
    },
});
