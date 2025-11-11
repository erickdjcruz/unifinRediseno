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
 * The container of Omnichannel search tab.
 *
 * @class View.Layouts.Base.OmnichannelSearchLayout
 * @alias SUGAR.App.view.layouts.BaseOmnichannelSearchLayout
 * @extends View.Views.Base.QuicksearchLayout
 */

({
    extendsFrom: 'QuicksearchLayout',
    className: 'omnichannel-search',

    componentsToAdd: [
        'omnichannel-search-modulelist',
        'omnichannel-search-button',
        'omnichannel-search-bar',
        'omnichannel-search-results',
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // Remove unneeded event from the parent quicksearch layout
        this.off('quicksearch:close');

        var dashboard = this.closestComponent('omnichannel-dashboard');
        if (dashboard && dashboard.searchCollection) {
            this.collection.off();
            this.collection = dashboard.searchCollection;
        }
    },
    /**
     * @inheritdoc
     */
    _placeComponent: function(component) {
        if (_.contains(this.componentsToAdd, component.name)) {
            this.$('[data-component=searchbar-inputs]').append(component.el);
        } else {
            this._super('_placeComponent', [component]);
        }
    },

    /**
     * Save the search collection so it can be used if we ever recreate this component
     * @private
     */
    _dispose: function() {
        // While this is a little dangerous, we need to save the data whenever the search tab gets disposed
        // searchCollection should get disposed later when omnichannel-dashboard is disposed
        var dashboard = this.closestComponent('omnichannel-dashboard');
        if (dashboard) {
            dashboard.searchCollection = this.collection;
        }
        this._super('_dispose');
    }
})
