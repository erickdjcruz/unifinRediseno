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
 * @class View.Views.Base.OmnichannelSearchButtonView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchButtonView
 * @extends View.Views.Base.QuicksearchButtonView
 */
({
    extendsFrom: 'QuicksearchButtonView',
    className: 'table-cell omnichannel-search-button-wrapper',

    events: {
        'click [data-action=search_icon]': 'searchIconClickHandler'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.layout.on('omnichannelsearch:close', function() {
            if (!this.context.get('search')) {
                this.toggleSearchIcon(true);
            }
        }, this);

        /**
         * Used for indicating the state of the button icon.
         *
         * @property {boolean}
         * - `true` means magnifying glass.
         * - `false` means X icon.
         */
        this.searchButtonIcon = true;
    },

    /**
     * Toggles the search icon between the magnifying glass and x.
     *
     * @param {boolean} searchButtonIcon Indicates the state of the search button icon
     * - `true` means magnifying glass.
     * - `false` means X icon.
     */
    toggleSearchIcon: function(searchButtonIcon) {
        if (this.searchButtonIcon === searchButtonIcon) {
            return;
        }
        var iconEl = this.$('[data-action=search_icon] .sicon').first();
        this.searchButtonIcon = searchButtonIcon;
        if (searchButtonIcon) {
            iconEl.removeClass('sicon-close');
            iconEl.addClass('sicon-search');
        } else {
            iconEl.removeClass('sicon-search');
            iconEl.addClass('sicon-close');
        }
    },

    /**
     * Handler for clicks on the search icon (or x, depending on state).
     */
    searchIconClickHandler: function() {
        if (this.searchButtonIcon) {
            app.events.trigger('omnichannelsearch:bar:search:term');
        } else {
            this.layout.trigger('omnichannelsearch:bar:clear:term');
            this.layout.trigger('omnichannelsearch:close');
        }
    }
})
