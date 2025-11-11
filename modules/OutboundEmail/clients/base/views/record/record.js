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
 * @class View.Views.Base.OutboundEmail.RecordView
 * @alias SUGAR.App.view.views.BaseOutboundEmailRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    /**
     * Checks if authorized for google oauth2.
     *
     * @inheritdoc
     */
    saveClicked: function() {
        if (this.model.get('mail_authtype') === 'oauth2' && !this.model.get('eapm_id')) {
            app.alert.show('oe-edit', {
                level: 'error',
                title: '',
                messages: [app.lang.get('LBL_EMAIL_PLEASE_AUTHORIZE', this.module)]
            });
        } else {
            this._super('saveClicked');
        }
    }
})
