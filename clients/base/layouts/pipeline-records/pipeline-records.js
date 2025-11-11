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
 * @class View.Layouts.Base.PipelineRecordsLayout
 * @alias SUGAR.App.view.layouts.BasePipelineRecordsLayout
 */
({
    className: 'pipeline-records',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.pipelineModules = app.metadata.getModule('VisualPipeline', 'config').enabled_modules || [];
    },

    /**
     * Loads data for a particular user and renders the pipelineType on callback success
     * @param options
     */
    loadData: function(options) {
        var filter = {
            '$or': [
                {'assigned_user_id': {'$equals': app.user.get('id')}}
            ]
        };

        this.collection.setOption('filter', filter);
        this.collection.setOption('params', {order_by: 'date_modified:DESC'});
        this.collection.setOption('limit', 2); // At most 2 rows - default config and user config (if any).
        this.collection.fetch();
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        if (this.$('.btn-group.pipeline-refresh-btn')[0] &&
            this.$('.btn-group.pipeline-refresh-btn')[0].firstElementChild) {
            // Change the label of refresh button to say 'Refresh Tiles' instead on 'Refresh list'
            this.$('.btn-group.pipeline-refresh-btn')[0].firstElementChild.title = app.lang.get('LBL_TILE_REFRESH');
        }
    }
})
