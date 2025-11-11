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
({
    extendsFrom: 'BaseView',

    plugins: ['Stage2CssLoader'],

    /**
     * Set keys
     */
    setKeys: function() {
        this.keys = [{
            icon: 'sicon-news-lg',
            title: app.lang.get('LBL_NOTIFICATIONS_KEY_TITLE_DASHLET')
        }, {
            icon: 'sicon-bell-lg',
            title: app.lang.get('LBL_NOTIFICATIONS_KEY_TITLE_BROWSER'),
            details: app.lang.get('LBL_NOTIFICATIONS_KEY_DESCR_BROWSER')
        }, {
            icon: 'sicon-bell-cross-lg',
            disabledClass: 'icon-disabled',
            details: app.lang.get('LBL_NOTIFICATIONS_KEY_DESCR_BROWSER_DISABLED')
        }, {
            icon: 'sicon-email-lg',
            title: app.lang.get('LBL_NOTIFICATIONS_KEY_TITLE_EMAIL')
        }, {
            icon: 'sicon-calendar-lg',
            title: app.lang.get('LBL_NOTIFICATIONS_KEY_TITLE_DAILY'),
        }, {
            icon: 'sicon-calendar-lg',
            title: app.lang.get('LBL_NOTIFICATIONS_KEY_TITLE_WEEKLY'),
        }];
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.setKeys();
        this._super('_render');
    }
});
