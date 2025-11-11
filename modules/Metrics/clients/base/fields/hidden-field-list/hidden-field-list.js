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
 * @class View.Fields.Base.Metrics.HiddenFieldListField
 * @alias SUGAR.App.view.fields.BaseMetricsHiddenFieldListField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * List of fields that are displayed for a given column.
     */
    hiddenFields: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        let url = app.api.buildURL('Metrics', 'hidden', null, {
            metric_context: this.context.get('metric_context') || 'service_console',
            metric_module: this.context.get('metric_module') || 'Cases'
        });
        app.api.call('GET', url, null, {
            success: _.bind(function(results) {
                this.hiddenFields = [];
                if (!_.isEmpty(results)) {
                    _.each(results, function(field) {
                        this.hiddenFields.push({
                            'name': field.id,
                            'displayName': field.name
                        });
                    }, this);
                }
                this._super('_render');
                this.handleDragAndDrop();
            }, this),
        });
    },

    /**
     * Handles the dragging of the items from available fields list to the columns list section
     * But not the way around
     */
    handleDragAndDrop: function() {
        this.$('#fields-sortable').sortable({
            connectWith: '.connectedSortable',
            receive: _.bind(function(event, ui) {
                ui.sender.sortable('cancel');
            }, this)
        });
    }
})
