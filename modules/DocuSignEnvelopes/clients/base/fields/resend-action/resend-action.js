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
 * @class View.Fields.Base.DocuSignEnvelopes.ResendActionField
 * @alias SUGAR.App.view.fields.BaseDocuSignEnvelopesResendActionField
 * @extends View.Fields.Base.RowactionField
 */
({
    extendsFrom: 'RowactionField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.events = _.extend({}, this.events, {
            'click [data-action=resend]': 'resend'
        });
    },

    /**
     * Resend
     */
    resend: function() {
        if (this.model.get('status') !== 'sent') {
            app.alert.show('info-resend-envelope', {
                level: 'warning',
                messages: app.lang.get('LBL_ENVELOPE_NOT_SENT', 'DocuSignEnvelopes')
            });

            return;
        }

        app.alert.show('resend_completed', {
            level: 'process',
            title: app.lang.get('LBL_LOADING')
        });

        let options = {
            id: this.model.get('id')
        };
        app.api.call('create', app.api.buildURL('DocuSign', 'resendEnvelope'), options, {
            success: function(res) {
                if (res.status == 'error') {
                    app.alert.show('error-resend-envelope', {
                        level: 'error',
                        messages: res.message,
                        autoClose: false
                    });

                    return;
                }

                app.alert.show('success-resent-envelope', {
                    level: 'success',
                    messages: app.lang.get('LBL_ENVELOPE_SENT', 'DocuSignEnvelopes'),
                    autoClose: true
                });
                this.model.fetch();
            }.bind(this),
            error: function(error) {
                app.alert.show('error-resend-envelope', {
                    level: 'error',
                    messages: error
                });
            },
            complete: function() {
                app.alert.dismiss('resend_completed');
            }
        });
    }
});
