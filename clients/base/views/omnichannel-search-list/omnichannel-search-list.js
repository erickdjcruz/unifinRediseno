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
 * @class View.Views.Base.OmnichannelSearchListView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchListView
 * @extends View.Views.Base.SearchListView
 */
({
    extendsFrom: 'SearchListView',

    className: 'omnichannel-search-list-wrapper',

    /**
     * @inheritdoc
     */
    'events': {
        'click .search-result': 'rowClicked',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // Share the same collection as other views in the parent layout
        this.collection = this.layout.collection;
        this.collection.on('sync', this.displaySearchPage, this);

        app.events.trigger('omnichannel:modulelist:open');
        this.layout.on('omnichannelsearch:clear:results', this._clearSearchResults, this);
    },

    /**
     * Clears search results
     *
     * @private
     */
    _clearSearchResults: function() {
        if (!this.collection.showMore) {
            this.render();
        }
    },

    /**
     * Used by the Pagination plugin to retrieve fetch options for the
     * collection before paginating
     *
     * @return {Object} The set of collection fetch options to use for pagination
     */
    getPaginationOptions: function() {
        // Start with default options for the pagination
        var options = {
            apiOptions: {
                data: {},
                fetchWithPost: true,
                useNewApi: true
            }
        };

        // Make sure pagination uses the currently selected facets
        var selectedFacets = this.context.get('selectedFacets');
        if (selectedFacets) {
            options.apiOptions.data.agg_filters = selectedFacets;
        }

        return options;
    },

    /**
     * Displays search page
     *
     * @param {Event} evt row click event
     */
    displaySearchPage: _.debounce(function() {
        if (this.disposed || !this.collection instanceof App.BeanCollection) {
            return;
        }

        this.collection.showMore = false;

        this.parseModels(this.collection.models);
        if (this._previewed) {
            app.events.trigger('preview:close');
        }

        $('.omnichannel-search-list')
            .removeClass('omnichannel-search-backdrop');
        this.render();
    }, 200),

    /**
     * Handler for row click event
     * Switches to relevant module tab
     *
     * @param {Event} evt row click event
     */
    rowClicked: function(evt) {
        var loadModel = [];
        var rowId = evt.currentTarget && evt.currentTarget.dataset ? evt.currentTarget.dataset.id : '';
        var dashboard = this.closestComponent('omnichannel-dashboard');
        if (!_.isEmpty(rowId) && dashboard && dashboard.searchCollection) {
            loadModel = _.filter(dashboard.searchCollection.models, function(model) {
                if (model.id === rowId) {
                    return model;
                }
            }, this);
        }

        // set current row as model for the dashboard tab and switch tab based on module
        if (dashboard && loadModel.length !== 0) {
            app.alert.show('data-load', {
                level: 'process',
                title: app.lang.get('LBL_LOADING'),
            });
            loadModel[0].fetch({
                success: _.bind(function(model) {
                    app.alert.dismiss('data-load');
                    if (this.disposed) {
                        return;
                    }
                    dashboard.setModel(dashboard.moduleTabIndex[model.get('_module')], model);
                    dashboard.switchTab(dashboard.moduleTabIndex[model.get('_module')]);
                }, this)
            });
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.collection.off('sync', this.displaySearchPage, this);
        this.layout.off('omnichannelsearch:clear:results', this._clearSearchResults, this);
        this._super('_dispose');
    }
})
