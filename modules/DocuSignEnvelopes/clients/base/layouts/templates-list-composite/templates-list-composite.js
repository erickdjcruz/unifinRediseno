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
 * @class View.Layouts.Base.DocuSignEnvelopes.TemplatesListCompositeLayout
 * @alias SUGAR.App.view.layouts.BaseDocuSignEnvelopesTemplatesListCompositeLayout
 * @extends View.Layouts.Base.SelectionListLayout
 */
({
    extendsFrom: 'SelectionListLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._registerEvents();
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'filter:apply', this._resetCollection);
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
        this.data = data;

        this._resetCollection();

        this.render();
    },

    /**
     * Reset collection
     *
     * @param {string} newSearch
     */
    _resetCollection: function(newSearch) {
        this.collection.reset();

        this._buildCollection(newSearch);

        this.context.trigger('templates:loaded');
    },

    /**
     * Build collection
     * Filter by newSearch considering existing filter functionality. % can be used as placehoder
     *
     * @param {string} newSearch
     */
    _buildCollection: function(newSearch) {
        if (_.isUndefined(newSearch)) {
            newSearch = '';
        }

        const sugarPlaceholder = '%';
        const regexPlaceholder = '.';

        newSearch = newSearch.toLowerCase();
        newSearch = newSearch.replaceAll(sugarPlaceholder, regexPlaceholder);
        newSearch = newSearch.replaceAll(' ', '');

        if (!_.isEmpty(newSearch) && newSearch.substring(0, 1) !== regexPlaceholder) {
            newSearch = '^' + newSearch;
        }
        if (!_.isEmpty(newSearch) && newSearch.substring(newSearch.length - 1) !== regexPlaceholder) {
            newSearch = newSearch + '.';
        }

        const regExForSearch = new RegExp(newSearch);

        _.each(this.data.templates, function(templateData) {
            let templateName = templateData.name.toLowerCase();
            templateName = templateName.replaceAll(' ', '');

            if (_.isNull(regExForSearch.exec(templateName))) {
                return;
            }

            const model = app.data.createBean(undefined, {
                id: templateData.id,
                name: templateData.name,
                _module: 'DocuSignEnvelopes'
            });
            this.collection.add(model);
        }, this);

        this.collection.dataFetched = true;
        this.collection.offset = -1;
    },
});
