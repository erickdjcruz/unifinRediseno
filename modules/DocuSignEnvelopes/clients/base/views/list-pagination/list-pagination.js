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
 * @class View.Views.Base.DocuSignEnvelopes.ListPaginationView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesListPaginationView
 * @extends View.Views.Base.ListPaginationView
 */
({
    extendsFrom: 'ListPaginationView',

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
        this.listenTo(this.context, 'recipients:loaded', this.setCache, this);
    },

    /**
     * @inheritdoc
     */
    getPageCount: function() {
        if (!this.context.get('docusign_recipients')) {
            this._super('getPageCount');
            return;
        }

        this.fetchCount();
    },

    /**
     * @inheritdoc
     */
    fetchCount: function() {
        if (!this.context.get('docusign_recipients')) {
            this._super('fetchCount');
            return;
        }

        const total = this.context.get('totalNumberOfRecipients');
        this.collection.trigger('list:page-total:fetched', total);
    },

    /**
     * @inheritdoc
     */
    getPage: function(page) {
        if (!this.context.get('docusign_recipients')) {
            this._super('getPage', [page]);
            return;
        }

        this.page = page;

        if (this.restoreFromCache()) {
            // update count label
            this.context.trigger('list:paginate');

            this.render();
        } else {
            this.context.trigger('load:recipients');
        }
    },
})
