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
 * @class View.Views.Base.KpiMetricsToolsView
 * @alias SUGAR.App.view.layouts.BaseKpiMetricsToolsView
 * @extends View.View
 */
({
    plugins: ['Dropdown'],

    className: 'kpi-metrics-tools',

    events: {
        'click .organize': 'organize',
        'click .create-new': 'createNew',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.isAdmin = (app.user.get('type') === 'admin');
        this._super('initialize', [options]);
    },

    /**
     * "organize" button click event listener
     */
    organize: function() {
        app.drawer.open({
            layout: 'config-drawer',
            context: {
                module: 'Metrics',
                metric_context: this.layout.meta.metric_context,
                metric_module: this.layout.meta.metric_module
            }
        });
    },

    /**
     * "Create New" button click event listener
     */
    createNew: function() {
        let prefill = app.data.createBean('Metrics');
        prefill.set({
            metric_context: this.layout.meta.metric_context,
            metric_module: this.layout.meta.metric_module,
            status: 'Inactive',
            order_by_primary: this.layout.meta.order_by_primary,
            order_by_primary_direction: 'asc',
            order_by_secondary: '',
            order_by_secondary_direction: 'asc',
            freeze_first_column: true
        });
        app.drawer.open({
            layout: 'record',
            context: {
                module: 'Metrics',
                model: prefill,
            }
        });
    }
})
