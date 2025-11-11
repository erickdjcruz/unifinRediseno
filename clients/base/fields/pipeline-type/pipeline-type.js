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
 * @class View.Fields.Base.PipelineTypeField
 * @alias SUGAR.App.view.fields.BasePipelineTypeField
 * @extends View.Fields.Base.BaseField
 */

({
    /**
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.getTabs();
    },

    /**
     * Getting various PipelineType tabs with their header and module fields
     */
    getTabs: function() {
        this.tabs = [];
        var fieldsForTabs = [];
        var config = app.metadata.getModule('VisualPipeline', 'config');
        fieldsForTabs.push(config.table_header[this.module]);
        var fieldMeta = app.metadata.getModule(this.module, 'fields');

        _.each(fieldsForTabs, function(field) {
            var fieldLabel = app.lang.getModString(fieldMeta[field].vname, this.module);
            var metaObject = {
                headerLabel: fieldLabel,
                moduleField: field,
                tabLabel: app.lang.get('LBL_PIPELINE_VIEW_TAB_NAME', this.module, {
                    module: app.lang.getModuleName(this.module, {plural: true}),
                    fieldName: fieldLabel
                })
            };
            this.tabs.push(metaObject);
        }, this);
    }
});
