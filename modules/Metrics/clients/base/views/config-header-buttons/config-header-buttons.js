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
 * @class View.Views.Base.Metrics.ConfigHeaderButtonsView
 * @alias SUGAR.App.view.views.BaseMetricsConfigHeaderButtonsView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.model.set({
            metric_context: this.context.get('metric_context') || 'service_console',
            metric_module: this.context.get('metric_module') || 'Cases'
        });
    },

    _beforeSaveConfig: function() {
        let columnsSortable = [];
        let columnLists = this.layout.$el.find('#columns-sortable li');
        _.each(columnLists, function(li) {
            columnsSortable.push(li.getAttribute('fieldname'));
        });
        let fieldsSortable = [];
        let fieldLists = this.layout.$el.find('#fields-sortable li');
        _.each(fieldLists, function(li) {
            fieldsSortable.push(li.getAttribute('fieldname'));
        });
        this.context.get('model').set({
            is_setup: true,
            visible_list: columnsSortable,
            hidden_list: fieldsSortable
        }, {silent: true});
        return this._super('_beforeSaveConfig');
    },

    /**
     * Calls the context model save and saves the config model in case
     * the default model save needs to be overwritten
     *
     * @protected
     */
    _saveConfig: function() {
        this._super('_saveConfig');
    },
})
