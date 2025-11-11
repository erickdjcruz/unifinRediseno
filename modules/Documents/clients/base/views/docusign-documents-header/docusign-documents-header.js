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
 * @class View.Views.Base.Documents.DocusignDocumentsHeaderView
 * @alias SUGAR.App.view.views.BaseDocumentsDocusignDocumentsHeaderView
 * @extends View.Views.Base.View
 */
({
    className: 'docusign-documents-header',

    events: {
        'click a[name=clear_button]': 'clear',
        'click .addDocument': 'openDocumentsSelectionList',
        'click .sendEnvelope': 'sendToDocuSign',
        'click .selectTemplate': 'selectTemplate',
        'click .sendWithTemplate': 'sendWithTemplate'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.listenTo(this.collection, 'change add remove', _.bind(this.toggleFieldsAccessability, this));
    },

    /**
     * Toggle fields accessability
     */
    toggleFieldsAccessability: function() {
        var clearButton = this.$('a[name=clear_button]');
        var sendButton = this.$('.sendEnvelope');

        if (this.collection.models.length === 0) {
            clearButton.hide();
            sendButton.addClass('disabled');
        } else {
            clearButton.show();
            sendButton.removeClass('disabled');
        }
    },

    /**
     * Clear collection
     */
    clear: function() {
        if (this.collection.models.length === 0) {
            return;
        }
        this.collection.reset();
    },

    /**
     * Open documents selection list
     */
    openDocumentsSelectionList: function() {
        app.drawer.open({
            layout: 'multi-selection-list',
            context: {
                module: 'Documents',
                isMultiSelect: true
            }
        }, _.bind(function(models) {
            if (!models) {
                return;
            }

            this.collection.add(models);
        }, this));
    },

    /**
     * Send to DocuSign
     */
    sendToDocuSign: function() {
        if (this.collection.models.length === 0) {
            return;
        }
        this.context.parent.trigger('sendDocumentsToDocuSign');
    },

    /**
     * Select template
     */
    selectTemplate: function() {
        this.context.parent.trigger('selectTemplate', 'selectTemplate');
    },

    /**
     * Select template
     */
    sendWithTemplate: function() {
        this.context.parent.trigger('sendWithTemplate', 'sendWithTemplate');
    },

    _render: function() {
        this._super('_render');

        this.toggleFieldsAccessability();
    }
})
