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
({
    extendsFrom: 'FilterLayout',

    /**
     * @inheritdoc
     *
     * Change layout type to 'records' for pipeline view.
     */
    getFilterEditStateKey: function() {
        if (this.layoutType === 'pipeline-records') {
            return app.user.lastState.key('edit-' + this.layout.currentModule + '-records', this);
        } else {
            return this._super('getFilterEditStateKey');
        }
    },

    /**
     * @inheritdoc
     *
     * Applies pipeline filters if layout type is pipeline-records
     */
    applyFilter: function(query, dynamicFilterDef) {
        if (this.layoutType === 'pipeline-records') {
            this.filterPipeline(query, dynamicFilterDef);
        } else {
            this._super('applyFilter', [query, dynamicFilterDef]);
        }
    },

    /**
     * Gets called when the filters are added/removed, and triggers filterChanged listener
     * @param {string} query search string
     * @param {Object} dynamicFilterDef(optional)
     */
    filterPipeline: function(query, dynamicFilterDef) {
        if (_.isEmpty(query)) {
            var filterQuicksearchView = this.getComponent('filter-quicksearch');
            query = filterQuicksearchView && filterQuicksearchView.$el.val() || '';
        }

        var ctxCollection = this.context.get('collection');
        var origFilterDef = dynamicFilterDef || ctxCollection.origFilterDef || [];
        var filterDef = this.buildFilterDef(origFilterDef, query, this.context);

        ctxCollection.filterDef = filterDef;
        ctxCollection.origFilterDef = origFilterDef;
        this.context.trigger('pipeline:recordlist:filter:changed', filterDef);
    },

    /**
     * @inheritdoc
     *
     * Adds the pipeline-records context to relevant context list if on layout
     */
    getRelevantContextList: function() {
        var contextList = [];
        if (this.layoutType === 'pipeline-records') {
            var context = this.context;
            if (!context.get('modelId') && context.has('collection')) {
                contextList.push(context);
            }
        } else {
            contextList = this._super('getRelevantContextList');
        }
        return _.uniq(contextList);
    }
})
