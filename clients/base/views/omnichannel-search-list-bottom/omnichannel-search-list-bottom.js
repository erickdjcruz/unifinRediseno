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
 * @class View.Views.Base.OmnichannelSearchListBottomView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchListBottomView
 * @extends View.Views.Base.ListBottomView
 */
({
    plugins: ['Pagination'],
    extendsFrom: 'ListBottomView',

    events: {
        'click [data-action="show-more"]': 'showMoreRecords'
    },

    /**
     * Flag for if the loading placeholder text should be hidden or not
     *
     * @property {boolean}
     */
    showLoading: false,

    initialize: function(options) {
        this._super('initialize', [options]);

        // Share the same collection as other views in the parent layout
        this.collection = this.layout.collection;
        this.collection.on('sync', this._renderAfterFetch, this);

        this.action = 'list';

        this._initPagination();

        this._showMoreLabel = this.meta && this.meta.label || 'TPL_SHOW_MORE_MODULE';

        // Listener for when a search is triggered. Show "Loading..."
        app.events.on('omnichannelsearch:bar:search:started', this._showLoading, this);
        this.layout.on('omnichannelsearch:filtering:started', this._showLoading, this);
    },

    /**
     * Handles rerendering the view after the collection has been re-fetched
     *
     * @private
     */
    _renderAfterFetch: function() {
        this.showLoading = false;
        this.render();
    },

    /**
     * Initialize pagination component in order to react the show more link.
     * @private
     */
    _initPagination: function() {
        this.paginationComponent = _.find(this.layout._components, function(component) {
            return _.contains(component.plugins, 'Pagination');
        }, this);
    },

    /**
     * Show "Loading..."
     * @private
     */
    _showLoading: _.debounce(function() {
        this.collection.dataFetched = false;
        this.showLoading = true;
        this.render();
        this.layout.trigger('omnichannelsearch:clear:results');
    }, 200),

    /**
     * Retrieving the next page records by pagination plugin.
     *
     * Please see the {@link app.plugins.Pagination#getNextPagination}
     * for detail.
     */
    showMoreRecords: function() {
        if (!this.paginationComponent) {
            return;
        }

        this.collection.offset = this.collection.next_offset;
        this.collection.showMore = true;
        this._showLoading();
        this.collection.dataFetched = true;
        this.getNext();
        this.render();
    },

    /**
     * Waits for 0.1 seconds before calling getNextPagination
     * from pagination component
     */
    getNext: _.debounce(function(options) {
        this.paginationComponent.getNextPagination(options);
    }, 100),

    /**
     * Assign proper label for 'show more' link.
     * Label should be "More <module name>..." if module_list contains one module.
     * Label should be "More search results..." if module_list contains multiple modules.
     */
    setShowMoreLabel: function() {
        if (!this.collection) {
            return;
        }
        if (this.collection.dataFetched &&
            !_.isEmpty(this.collection.models) &&
            !_.isEmpty(this.collection.module_list)) {
            if (this.collection.module_list.length > 1) {
                this.showMoreLabel = app.lang.get('LBL_SHOW_MORE_RESULTS');
            } else {
                var model = this.collection.models[0] ? this.collection.models[0] : {};
                var module = model ? model.get('_module') : '';
                var context = {
                    count: this.collection.length,
                    offset: this.collection.next_offset >= 0
                };
                if (module) {
                    context.module = new Handlebars.SafeString(
                        app.lang.getModuleName(module, {plural: true}).toLowerCase()
                    );
                }
                this.showMoreLabel = app.lang.get(this._showMoreLabel, module, context);
            }
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.collection.off('sync', this._renderAfterFetch, this);
        app.events.off('omnichannelsearch:bar:search:started', this._showLoading, this);
        this.layout.off('omnichannelsearch:filtering:started', this._showLoading, this);
        this._super('_dispose');
    }
})
