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
 * @class View.Views.Portal.ContentsearchResultsView
 * @alias SUGAR.App.view.views.PortalContentsearchResultsView
 * @extends View.View
 * @deprecated since 10.2, will be removed in the future.
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        app.logger.warn('View.Views.Portal.ContentsearchDropdownLayout is deprecated since 10.2 and will be' +
            ' removed in the future');
        this._super('initialize', [options]);
        this.dataFetched = false;
        this.records = [];
        this.context.on('data:fetching', this.showFetching, this);
        this.context.on('data:fetched', this.showData, this);
    },

    /**
     * Shows data.
     *
     * @param {Object} data The data to show
     */
    showData: function(data) {
        this.records = data.records;
        this.dataFetched = true;
        this.render();
    },

    /**
     * Shows message 'Searching...'.
     */
    showFetching: function() {
        this.records = [];
        this.dataFetched = false;
        this.render();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('data:fetching', null, this);
            this.context.off('data:fetched', null, this);
        }
    }
})
