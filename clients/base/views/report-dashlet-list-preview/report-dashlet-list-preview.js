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
 * @class View.Views.Base.ReportDashletDataTablePreviewView
 * @alias SUGAR.App.view.views.BaseReportDashletDataTablePreviewView
 * @extends View.View
 */
 ({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this._reportMapping = {
            detailed_summary: 'summation-with-detail-skeleton-loader',
            Matrix: 'matrix-skeleton-loader',
            summary: 'summation-skeleton-loader',
            tabular: 'rows-and-columns-skeleton-loader',
        };

        this._dashletTitle = this.model.get('label');
        this._showFooter = this.model.get('showTotalRecordCount');
        this._reportType = this._reportMapping[this.model.get('reportType')];
    },

    /**
     * Refresh the UI
     */
    refreshPreview: function() {
        this._initProperties();
        this.render();
    },
})
