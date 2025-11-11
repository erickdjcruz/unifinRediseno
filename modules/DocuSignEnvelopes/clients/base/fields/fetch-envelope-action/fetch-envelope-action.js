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
 * @class View.Fields.Base.DocuSignEnvelopes.FetchEnvelopeActionField
 * @alias SUGAR.App.view.fields.BaseDocuSignEnvelopesFetchEnvelopeActionField
 * @extends View.Fields.Base.RowactionField
 */
({
    extendsFrom: 'RowactionField',

    /**
     * @inheritDoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.events = _.extend({}, this.events, {
            'click [data-action=fetchEnvelope]': 'fetchEnvelope'
        });
    },

    /**
     * Fetch envelope
     */
    fetchEnvelope: function() {
        app.alert.show('fetch_envelope', {
            level: 'process',
            title: app.lang.get('LBL_LOADING')
        });

        let options = {
            id: this.model.get('id')
        };
        app.api.call('create', app.api.buildURL('DocuSign', 'updateEnvelope'), options, {
            success: function(res) {
                if (res && res.status && res.status === 'error') {
                    app.alert.show('error-fetch-envelope', {
                        level: 'error',
                        messages: res.message,
                        autoClose: false
                    });

                    return;
                }

                app.alert.show('success-fetch-envelope', {
                    level: 'success',
                    messages: app.lang.get('LBL_DRAFT_CHANGED_SUCCESS', 'DocuSignEnvelopes'),
                    autoClose: true
                });
                this.model.fetch();
            }.bind(this),
            error: function(error) {
                app.alert.show('error-fetch-envelope', {
                    level: 'error',
                    messages: error
                });
            },
            complete: function() {
                app.alert.dismiss('fetch_envelope');
            }
        });
    }
});
