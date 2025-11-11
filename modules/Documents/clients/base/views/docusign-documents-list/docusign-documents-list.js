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
 * @class View.Views.Base.Documents.DocusignDocumentsListView
 * @alias SUGAR.App.view.views.BaseDocumentsDocusignDocumentsListView
 * @extends View.Views.Base.View
 */
 ({
    extendsFrom: 'RecordlistView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.filter(this.plugins, function(pluginName) {
            return pluginName !== 'ResizableColumns' && pluginName !== 'ReorderableColumns';
        });

        this._super('initialize', [options]);

        this.context.on('list:document:remove', this.handleDocumentRemove, this);
    },

    _initializeMetadata: function() {
        return app.metadata.getView('Documents', 'docusign-documents-list') || {};
    },

    /**
     * @inheritdoc
     */
    _loadTemplate: function(options) {
        this.tplName = 'recordlist';
        this.template = app.template.getView(this.tplName);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.leftColumns = [];
        this._super('_render');
    },

    /**
     * Handle document remove
     *
     * @param {Object} model
     */
    handleDocumentRemove: function(model) {
        this.collection.remove(model);
    },

    /**
     * @inheritdoc
     */
    setOrderBy: function() {
        return;
    },

    /**
     * @inheritdoc
     */
    freezeFirstColumn: function(event) {
        event.stopPropagation();
        let freeze = $(event.currentTarget).is(':checked');
        this.isFirstColumnFreezed = freeze;
        app.user.lastState.set(this._thisListViewUserConfigsKey, {freezeFirstColumn: freeze});
        let $firstColumns = this.$('table tbody tr td:nth-child(1), table thead tr th:nth-child(1)');
        if (freeze) {
            $firstColumns.addClass('sticky-column stick-first');
        } else {
            $firstColumns.removeClass('sticky-column stick-first no-border');
        }
        this.showFirstColumnBorder();
    },

    /**
     * @inheritdoc
     */
    showFirstColumnBorder: function() {
        if (!this.isFirstColumnFreezed) {
            this.hasFirstColumnBorder = false;
            return;
        }
        let scrollPanel = this.$('.flex-list-view-content')[0];
        let firstColumnSelector = 'table tbody tr td:nth-child(1), table thead tr th:nth-child(1)';
        if (scrollPanel.scrollLeft === 0) {
            this.$(firstColumnSelector).addClass('no-border');
            this.hasFirstColumnBorder = false;
        } else if (!this.hasFirstColumnBorder) {
            this.$(firstColumnSelector).removeClass('no-border');
            this.hasFirstColumnBorder = true;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('list:document:remove');
        }
        this._super('_dispose');
    }
})
