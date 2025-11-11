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
 * List view for the {@link View.Layouts.Base.SearchLayout
 * Search layout}.
 *
 * @class View.Views.Base.OmnichannelSearchFilterView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchFilterView
 * @extends View.View
 */
({
    className: 'omnichannel-search-filter-wrapper w-4/12',

    events: {
        'click .omnichannel-search-facet-select': 'facetClicked'
    },

    /**
     * The collection facets/filters currently applied by the user
     */
    selectedFacets: {},

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // Share the same collection as other views in the parent layout
        this.collection = this.layout.collection;
        this.collection.on('sync', this.render, this);

        this.selectedFacets = {};

        // Pull previously selected facets on search
        var dashboard = this._getOmnichannelDashboard();
        if (dashboard && dashboard.searchSelectedFacets) {
            this.selectedFacets = dashboard.searchSelectedFacets;
        }

        app.events.on('omnichannelsearch:bar:search:started', this._handleBarSearchStarted, this);
        app.events.on('omnichannelsearch:bar:search:complete', this._handleBarSearchComplete, this);
    },

    /**
     * Handles when a search from the search bar is completed
     *
     * @private
     */
    _handleBarSearchComplete: function() {
        this._initializeSelectedFacets();
        this.render();
    },

    /**
     * Handles when pagination is initiated in the search list results
     *
     * @private
     */
    _handleBarSearchStarted: function() {
        this.collection.dataFetched = false;
        this.render();
    },

    /**
     * Get the omnichannel-dashboard component
     * @return {Data.Layout}
     * @private
     */
    _getOmnichannelDashboard: function() {
        return this.closestComponent('omnichannel-dashboard');
    },
    /**
     * Builds the selected facets object to be sent to the server when fetching
     *
     * @private
     */
    _initializeSelectedFacets: function() {
        var facets = this.collection.xmod_aggs;
        _.each(facets, function(facet, key) {
            if (key === 'modules') {
                this.selectedFacets[key] = [];
            } else {
                this.selectedFacets[key] = false;
            }
        }, this);

        this.context.set('selectedFacets', this.selectedFacets);

        var dashboard = this._getOmnichannelDashboard();
        if (dashboard) {
            dashboard.searchSelectedFacets = this.selectedFacets;
        }
    },

    /**
     * @inheritdoc
     *
     * Builds the facets objects for the template to display the module and
     * property facets correctly
     */
    _render: function() {
        this._initModuleFacets();
        this._initPropertyFacets();

        var dashboard = this._getOmnichannelDashboard();
        if (dashboard) {
            dashboard.searchModuleFacets = this.moduleFacets;
            dashboard.searchPropertyFacets = this.propertyFacets;
        }
        this._super('_render');
    },

    /**
     * Builds the module facets objects for the template
     *
     * @private
     */
    _initModuleFacets: function() {
        var moduleFacets = [];

        // The xmod_aggs collection property stores information about how many
        // records from each module facet are in the results
        var aggs = this.collection.xmod_aggs || {};
        var moduleResults = aggs && aggs.modules && aggs.modules.results;
        if (!_.isEmpty(moduleResults)) {
            _.each(moduleResults, function(count, moduleName) {
                moduleFacets.push({
                    facetId: 'modules',
                    facetCriteriaId: moduleName,
                    label: app.lang.getModuleName(moduleName, {plural: true}),
                    count: count,
                    selected: _.contains(this.selectedFacets.modules, moduleName)
                });
            }, this);
        }

        this.moduleFacets = moduleFacets;
    },

    /**
     * Builds the property facets objects for the template (Assigned to me,
     * Created by me, etc.)
     *
     * @private
     */
    _initPropertyFacets: function() {
        var propertyFacets = [];

        // The xmod_aggs collection property stores information about how many
        // records from each property facet are in the results
        var aggs = this.collection.xmod_aggs || {};
        var facetMeta = this.meta && this.meta.facets;
        _.each(facetMeta, function(facet) {
            var aggData = aggs[facet.facet_id];
            var count = aggData && aggData.results && aggData.results.count || 0;
            propertyFacets.push({
                facetId: facet.facet_id,
                facetCriteriaId: facet.facet_id,
                label: app.lang.get(facet.label),
                count: count,
                selected: this.selectedFacets[facet.facet_id] || false
            });
        }, this);

        this.propertyFacets = propertyFacets;
    },

    /**
     * Handles when a facet is selected/deselected by the user
     *
     * @param {Event} event the click event
     */
    facetClicked: function(event) {
        // Update the selected facets
        var currentTarget = this.$(event.currentTarget);
        var facetEl = currentTarget.closest('[data-facet-id]');
        var facetId = facetEl.data('facet-id');
        var facetCriteriaId = facetEl.data('facet-criteria-id');
        this._updateSelectedFacets(facetId, facetCriteriaId, facetId !== 'modules');

        // Refetch the collection with the updated list of selected facets
        var options = {
            apiOptions: {
                data: {
                    agg_filters: this.selectedFacets
                },
                fetchWithPost: true,
                useNewApi: true
            }
        };
        this.layout.trigger('omnichannelsearch:filtering:started');
        this.collection.fetch(options);
    },

    /**
     * Updates {@link #selectedFacets} with the facet change.
     *
     * @param {string} facetId The facet type
     * @param {string} facetCriteriaId The id of the facet criteria
     * @param {boolean} isSingleItem `true` if it's a single item facet
     * @private
     */
    _updateSelectedFacets: function(facetId, facetCriteriaId, isSingleItem) {
        if (isSingleItem) {
            this.selectedFacets[facetId] = !this.selectedFacets[facetId];
        } else {
            var index;
            if (this.selectedFacets[facetId]) {
                index = this.selectedFacets[facetId].indexOf(facetCriteriaId);
            } else {
                this.selectedFacets[facetId] = [];
            }
            if (_.isUndefined(index) || index === -1) {
                this.selectedFacets[facetId].splice(0, 0, facetCriteriaId);
            } else {
                this.selectedFacets[facetId].splice(index, 1);
                if (this.selectedFacets[facetId].length === 0) {
                    delete this.selectedFacets[facetId];
                }
            }
        }

        this.context.set('selectedFacets', this.selectedFacets);
        var dashboard = this._getOmnichannelDashboard();
        if (dashboard) {
            dashboard.searchSelectedFacets = this.selectedFacets;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.collection.off('sync', this.render, this);
        app.events.off('omnichannelsearch:bar:search:started', this._handleBarSearchStarted, this);
        app.events.off('omnichannelsearch:bar:search:complete', this._handleBarSearchComplete, this);
    }
})
