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
 * Layout for the global search results page.
 *
 * @class View.Layouts.Base.OmnichannelSearchListLayout
 * @alias SUGAR.App.view.layouts.BaseOmnichannelSearchListLayout
 * @extends View.Layouts.Base.SearchLayout
 */
({
    extendsFrom: 'SearchLayout',
    className: 'omnichannel-search-list flex flex-row flex-grow',

    componentsToAdd: [
        'omnichannel-search-headerpane',
        'omnichannel-search-list',
        'omnichannel-search-list-bottom',
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initSearchCollection();
    },

    /**
     * Initializes the collection to use within the layout and its views
     *
     * @private
     */
    _initSearchCollection: function() {
        this.collection = this.layout.collection;

        // Store a reference to the collection on the omnichannel dashboard
        var omnichannelDashboard = this.closestComponent('omnichannel-dashboard');
        if (!_.isEmpty(omnichannelDashboard)) {
            omnichannelDashboard.searchCollection = this.collection;
        }
    },

    /**
     * @inheritdoc
     */
    _placeComponent: function(component) {
        if (_.contains(this.componentsToAdd, component.name)) {
            this.$('[data-component=searchlist]').append(component.el);
        } else {
            this._super('_placeComponent', [component]);
        }
    }
})
