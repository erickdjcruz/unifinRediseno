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
 * @class View.Views.Base.Opportunities.PipelineRecordlistContentView
 * @alias App.view.views.BaseOpportunitiesPipelineRecordlistContentView
 * @extends View.Views.Base.PipelineRecordlistContentView
 */
({
    extendsFrom: 'PipelineRecordlistContentView',

    /**
     * Don't change the expected close date or the sales stage of an opp that is already closed
     * @inheritdoc
     */
    saveModel: function(model, pipelineData) {
        var oppCfg = app.metadata.getModule('Opportunities', 'config');
        var rliMode = oppCfg.opps_view_by === 'RevenueLineItems';

        if (_.contains(['date_closed', 'sales_stage'], this.headerField) && rliMode) {
            var forecastConfig = app.metadata.getModule('Forecasts', 'config') || {};
            var closedWon = forecastConfig.sales_stage_won || ['Closed Won'];
            var closedLost = forecastConfig.sales_stage_lost || ['Closed Lost'];
            var closedStatuses = closedWon.concat(closedLost);
            var status = model.get('sales_status');

            if (_.contains(closedStatuses, status)) {
                this._postChange(model, true, pipelineData);
                var moduleName = app.lang.getModuleName(this.module, {plural: false});
                var fieldLabel = app.metadata.getField({module: 'Opportunities', name: this.headerField}).vname;
                var fieldName = app.lang.get(fieldLabel, this.module);
                app.alert.show('error_converted', {
                    level: 'error',
                    messages: app.lang.get(
                        'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE',
                        this.module, {moduleSingular: moduleName, fieldName: fieldName}
                        )
                });
                return;
            }
        }

        this._super('saveModel', [model, pipelineData]);
    },

    /**
     * @inheritdoc
     */
    getFieldsForFetch: function() {
        var fields = this._super('getFieldsForFetch');
        var cfg = app.metadata.getModule('Opportunities', 'config');
        var newFields = ['closed_revenue_line_items'];

        if (cfg && cfg.opps_view_by) {
            newFields.push(cfg.opps_view_by === 'RevenueLineItems' ? 'sales_status' : 'sales_stage');
        }

        return _.union(fields, newFields, [this.headerField]);
    },

    /**
     * @inheritdoc
     */
    _setNewModelValues: function(model, ui) {
        var ctxModel = this.context.get('model');
        var $ulEl = this.$(ui.item).parent('ul');
        var headerFieldValue = $ulEl.attr('data-column-key');

        if (ctxModel && ctxModel.get('pipeline_type') === 'date_closed') {
            var dateClosed = app.date(headerFieldValue, 'MMMM YYYY')
                .endOf('month')
                .formatServer(true);

            model.set('date_closed', dateClosed);
            model.set('date_closed_cascade', dateClosed);
        } else {
            model.set(this.headerField, headerFieldValue);

            if (this.headerField === 'sales_stage') {
                model.set({
                    probability: app.utils.getProbabilityBySalesStage(headerFieldValue),
                    commit_stage: app.utils.getCommitStageBySalesStage(headerFieldValue),
                    sales_stage_cascade: headerFieldValue
                });
            }
        }
    }
});
