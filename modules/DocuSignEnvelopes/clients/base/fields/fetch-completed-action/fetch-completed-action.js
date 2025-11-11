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
 * @class View.Fields.Base.DocuSignEnvelopes.FetchCompletedActionField
 * @alias SUGAR.App.view.fields.BaseDocuSignEnvelopesFetchCompletedActionField
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
            'click [data-action=fetchCompleted]': 'fetchCompleted'
        });
    },

    /**
     * Fetch completed document
     */
    fetchCompleted: function() {
        if (this.model.get('status') !== 'completed') {
            app.alert.show('info-fetch-document', {
                level: 'info',
                messages: app.lang.get('LBL_ENVELOPE_NOT_COMPLETED', 'DocuSignEnvelopes')
            });

            return;
        }

        app.alert.show('fetch_completed', {
            level: 'process',
            title: app.lang.get('LBL_LOADING')
        });

        let options = {
            id: this.model.get('id')
        };
        app.api.call('create', app.api.buildURL('DocuSign', 'getCompletedDocument'), options, {
            success: function(res) {
                if (res && res.status && res.status === 'error') {
                    app.alert.show('error-getting-completed-document', {
                        level: 'error',
                        messages: res.message
                    });
                    return;
                }
                app.alert.show('success-fetch-document', {
                    level: 'success',
                    messages: app.lang.get('LBL_DOCUMENT_ADDED', 'DocuSignEnvelopes'),
                    autoClose: true
                });
                this.model.fetch();
            }.bind(this),
            error: function(error) {
                app.alert.show('error-getting-completed-document', {
                    level: 'error',
                    messages: error
                });
            },
            complete: function() {
                app.alert.dismiss('fetch_completed');
            }
        });
    }
});
