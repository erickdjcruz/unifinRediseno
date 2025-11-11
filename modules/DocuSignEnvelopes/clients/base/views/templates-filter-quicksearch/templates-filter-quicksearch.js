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
 * @class View.Views.Base.DocuSignEnvelopes.TemplatesFilterQuicksearchView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesTemplatesFilterQuicksearchView
 * @extends View.Views.Base.View
 */
({
    events: {
        'keyup': 'throttledSearch',
        'paste': 'throttledSearch',
        'click .add-on.sicon-close': 'clearInput'
    },

    /**
     * For customers with large datasets, allow customization to disable
     * the automatic filtering in the omnibar.
     *
     * @inheritdoc
     */
    delegateEvents: function(events) {
        if (app.config.disableOmnibarTypeahead) {
            // Remove the keyup and paste events from this.events.
            // This is before the call to this._super('delegateEvents'),
            // so they have not been registered.
            delete this.events.keyup;
            delete this.events.paste;

            // On enter key press, apply the quicksearch.
            this.events.keydown = _.bind(function(evt) {
                // Enter key code is 13
                if (evt.keyCode === 13) {
                    this._applyQuickSearch();
                }
            }, this);
        }
        this._super('delegateEvents', [events]);
    },

    /**
     * Fires the quick search.
     * @param {Event} [event] A keyup event.
     */
    throttledSearch: _.debounce(function(event) {
        this._applyQuickSearch();
    }, 400),

    /**
     * Append or remove an icon to the quicksearch input so the user can clear the search easily
     * @param {boolean} addIt TRUE if you want to add it, FALSE to remove
     */
    _toggleClearQuickSearchIcon: function(addIt) {
        if (addIt && !this.$('.sicon-close.add-on')[0]) {
            this.$el.append('<i class="sicon sicon-close add-on"></i>');
        } else if (!addIt) {
            this.$('.sicon-close.add-on').remove();
        }
    },

    /**
     * Clears out the filter search text for the layout
     */
    clearFilter: function() {
        this.currentSearch = '';
        this.$el.find('input').val('');
    },

    /**
     * Clear input
     */
    clearInput: function() {
        this.$el.find('input').val('');
        this._applyQuickSearch(true);
    },
    /**
     * Invokes the `filter:apply` event with the current value on the
     * quicksearch field.
     *
     * @param {boolean} [force] `true` to always trigger the `filter:apply`
     *   event, `false` otherwise. Defaults to `false`.
     */
    _applyQuickSearch: function(force) {
        force = !_.isUndefined(force) ? force : false;
        var newSearch = this.$el.find('input').val();
        if (force || this.currentSearch !== newSearch) {
            this.currentSearch = newSearch;
            this.context.trigger('filter:apply', newSearch);
        }

        //If the quicksearch field is not empty, append a remove icon so the user can clear the search easily
        this._toggleClearQuickSearchIcon(!_.isEmpty(newSearch));
    }
});
