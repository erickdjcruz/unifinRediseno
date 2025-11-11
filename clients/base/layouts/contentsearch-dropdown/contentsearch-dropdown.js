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
 * @class View.Views.Base.ContentsearchDropdownLayout
 * @alias SUGAR.App.view.views.BaseContentsearchDropdownLayout
 * @extends View.Layout
 */
({
    /**
     * @inheritdoc
     */
    className: 'contentsearch-dropdown',

    events: {
        'click [data-action="clicklink"]': 'linkClicked'
    },

    /**
     * Shows the dropdown
     */
    show: function() {
        $('body').on('click.contentsearch', _.bind(function(event) {
            if (!$.contains(this.el, event.target)) {
                this.$el.hide();
                $('body').off('click.contentsearch');
            }
        }, this));
        this.$el.show();
    },

    /**
     * Hides the dropdown
     */
    hide: function() {
        this.$el.hide();
    },

    /**
     * Opens a link in a new tab and hides the dropdown.
     *
     * @param {Event} evt The click event
     */
    linkClicked: function(evt) {
        var $target = this.$(evt.currentTarget);
        var url = $target.data('url');
        window.open(url, '_blank');
        this.hide();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $('body').off('click.contentsearch');
    }
})
