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
 * @class View.Fields.Base.QuicksearchButtonView
 * @alias SUGAR.App.view.fields.BaseQuicksearchButtonView
 * @extends View.View
 */
({
    className: 'quicksearch-button-wrapper min-w-[0rem] flex-1',

    events: {
        'click [data-action=search_icon]' : 'searchIconClickHandler'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.collection = this.layout.collection || app.data.createMixedBeanCollection();

        // Listener for `quicksearch:close`.
        this.layout.on('quicksearch:close', function() {
            if (!this.context.get('search')) {
                this.toggleSearchIcon(true);
            }
        }, this);

        /**
         * Used for keyboard up/down arrow navigation between components of `globalsearch` layout
         *
         * @property {boolean}
         */
        this.isFocusable = false;

        /**
         * Used for indicating the state of the button icon.
         *
         * @property {boolean}
         * - `true` means magnifying glass.
         * - `false` means X icon.
         */
        this.searchButtonIcon = true;

        this.layout.on('quicksearch:button:toggle', this.toggleSearchIcon, this);
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
            if (this.layout.isResponsiveMode) {
                this.layout.trigger('quicksearch:expand');
            } else {
                this.layout.trigger('quicksearch:bar:search');
            }
        } else {
            this.layout.trigger('quicksearch:bar:clear');
            this.layout.trigger('quicksearch:close');
        }
    }
})
