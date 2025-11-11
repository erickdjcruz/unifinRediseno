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
 * @class View.Layouts.Base.DocuSignEnvelopes.RecipientsListLayout
 * @alias SUGAR.App.view.layouts.BaseDocuSignEnvelopesRecipientsListLayout
 * @extends View.Layouts.Base.Layout
 */
({
    extendsFrom: 'MultiSelectionListLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.context.set({
            mass_collection: app.data.createBeanCollection(), //reset mass collection
            docusign_recipients: true
        });

        this._registerEvents();
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'load:recipients', this.loadData, this);
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        this._super('loadData', [options]);

        var apiData = {
            module: this.context.get('contextModule'),
            id: this.context.get('ctxModelId'),
            offset: this.collection.offset
        };
        app.api.call('read', app.api.buildURL('DocuSign', 'getListOfPossibleRecipients', {}, apiData), {}, {
            success: _.bind(this.successCallback, this),
            error: function() {
                app.alert.show('failed-to-fetch-recipients', {
                    level: 'error',
                    messages: app.lang.get('LBL_FAILED_FETCH_RECIPIENTS', 'DocuSignEnvelopes'),
                    autoClose: true
                });
            }
        });
    },

    /**
     * Success getting recipients
     *
     * @param {Object} data
     */
    successCallback: function(data) {
        if (this.disposed) {
            return;
        }

        this.collection.reset();

        _.each(data.recipients, function(recipientData) {
            const model = app.data.createBean(undefined, {
                id: recipientData.id,
                name: recipientData.name,
                email: recipientData.email,
                module: recipientData.module,
                _module: recipientData._module,
                type: recipientData.type,
            });
            this.collection.add(model);
        }, this);

        this.collection.dataFetched = true;
        this.collection.offset = data.nextOffset;
        this.context.set('totalNumberOfRecipients', data.totalNumberOfRecipients);
        this.render();

        this.context.trigger('recipients:loaded');
    },
});
