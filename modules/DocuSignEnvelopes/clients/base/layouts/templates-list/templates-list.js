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
 * @class View.Layouts.Base.DocuSignEnvelopes.TemplatesListLayout
 * @alias SUGAR.App.view.layouts.BaseDocuSignEnvelopesTemplatesListLayout
 * @extends View.Layouts.Base.SelectionListLayout
 */
 ({
    extendsFrom: 'SelectionListLayout',

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
        app.api.call('read', app.api.buildURL('DocuSign', 'listTemplates', {}, apiData), {}, {
            success: _.bind(this.successCallback, this),
            error: function() {
                app.alert.show('failed-to-fetch-templates', {
                    level: 'error',
                    messages: app.lang.get('LBL_FAILED_FETCH_TEMPLATES', 'DocuSignEnvelopes'),
                    autoClose: true
                });
            }
        });
    },

    /**
     * Success getting templates
     *
     * @param {Object} data
     */
    successCallback: function(data) {
        if (this.disposed) {
            return;
        }

        this.collection.reset();

        _.each(data.templates, function(templateData) {
            const model = app.data.createBean(undefined, {
                id: templateData.id,
                name: templateData.name,
                _module: 'DocuSignEnvelopes'
            });
            this.collection.add(model);
        }, this);

        this.collection.dataFetched = true;
        this.collection.offset = data.nextOffset;
        this.render();

        this.context.trigger('templates:loaded');
    },
});
