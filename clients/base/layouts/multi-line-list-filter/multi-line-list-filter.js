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
 * Layout for filtering a collection.
 *
 * Composed of a module dropdown(optional), a filter dropdown and an input.
 *
 * @class View.Layouts.Base.MultiLineListFilterLayout
 * @alias SUGAR.App.view.layouts.BaseFilterLayout
 * @extends View.Layouts.Base.FilterLayout
 */
({
    extendsFrom: 'FilterLayout',

    /**
     * Filter state
     */
    filterState: [],

    /**
     * True if multi-line list view has been initialized
     */
    isViewInitialized: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.filterState = [];
        this.isViewInitialized = false;
        this.listenTo(this.context, 'initialized:multi-line-list', _.bind(function() {
            this.isViewInitialized = true;
            this._super('initializeFilterState', this.filterState);
        }, this));
    },

    /**
     * @inheritdoc
     */
    initializeFilterState: function(moduleName, linkName, filterId) {
        this.filterState = [moduleName, linkName, filterId];
        if (this.isViewInitialized) {
            this._super('initializeFilterState', this.filterState);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    },
})
