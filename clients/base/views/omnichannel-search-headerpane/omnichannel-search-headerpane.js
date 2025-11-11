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
 * @class View.Views.Base.OmnichannelSearchHeaderpaneView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchHeaderpaneView
 * @extends View.Views.Base.SearchHeaderpaneView
 */
({
    extendsFrom: 'SearchHeaderpaneView',

    className: 'omnichannel-search-headerpane-wrapper',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // Share the same collection as other views in the parent layout
        this.collection = this.layout.collection;
        this.collection.on('sync', this.render, this);
    },

    /**
     * @inheritdoc
     *
     * Updates the results count string to show in the header pane
     */
    _render: function() {
        this.countLabel = '';
        this.searchTerm = {
            term: this.collection.query
        };
        var context = {
            num: this.collection.next_offset < 0 ? this.collection.total : this.collection.next_offset,
            total: this.collection.total
        };
        if (context.num || context.total) {
            this.countLabel = new Handlebars.SafeString(app.lang.get('TPL_LIST_HEADER_COUNT_TOTAL',
                this.module, context));
        }
        this._super('_render');
    },

    /**
     * Adjusts the title's ellipsis max-width when browser is resized.
     */
    adjustTitle: function() {
        var $titleCell = this.$('[data-name=omni-title]');
        var headerWidth = $('.omnichannel-search-list').width() * 0.6 - $('.omni-search-header').width();
        var count = $titleCell.find('.count').length > 0 ? $titleCell.find('.count').width() : 0;
        $(window).resize(function() {
            headerWidth = $('.omnibar-search').width() * 0.6 - $('.omni-search-header').width();
            count = $titleCell.find('.count').length > 0 ? $titleCell.find('.count').width() : 0;
        });
        headerWidth = count > 0 ? headerWidth - count : headerWidth;
        if ($titleCell && $titleCell.length > 0 && headerWidth > 0) {
            var $ellipsisDiv = $titleCell.find('.ellipsis_inline');
            $ellipsisDiv.css({
                'max-width': headerWidth * 0.92
            });
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.collection.off('sync', this.render, this);
        this._super('_dispose');
    }
})
