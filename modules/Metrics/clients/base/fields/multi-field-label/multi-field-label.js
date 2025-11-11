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
 * @class View.Fields.Base.Metrics.MultiFieldColumnLinkField
 * @alias SUGAR.App.view.fields.BaseMetricsMultiFieldColumnLinkField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'MetricsFieldListField',

    events: {
        'click .multi-field-label': 'multiFieldColumnLinkClicked'
    },

    /**
     * Create a new empty block and append it to the field list
     * @param e
     */
    multiFieldColumnLinkClicked: function(e) {
        var multiRow = app.lang.get('LBL_METRIC_MULTI_ROW', this.module);
        var multiRowHint = app.lang.get('LBL_METRIC_MULTI_ROW_HINT', this.module);
        var newMultiField = '<li class="pill outer multi-field-block">' +
            '<ul class="multi-field-sortable multi-field connectedSortable">' +
            '<li class="list-header" rel="tooltip" data-original-title="' + multiRow + '">' + multiRow +
            '<i class="sicon sicon-remove multi-field-column-remove"></i></li><div class="multi-field-hint">' +
            multiRowHint + '</div></ul></li>';

        var columnBox = $(e.currentTarget).closest('div.column').find('ul.field-list:first');
        columnBox.append(newMultiField);
        var newUl = columnBox.find('.multi-field-sortable.multi-field.connectedSortable:last');
        this.initMultiFieldDragAndDrop(newUl);
    }
})
