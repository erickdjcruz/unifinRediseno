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
 * @class View.Fields.Base.Metrics.ContextModuleField
 * @alias SUGAR.App.view.fields.BaseMetricsContextModuleField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        let fieldMeta = app.metadata.getModule('Metrics').fields.metric_context;
        let contextList = app.lang.getAppListStrings(fieldMeta.options);
        this.contextName = contextList[this.model.get('metric_context')];
        this.moduleName = this.model.get('metric_module');
        this.showNoData = false;
    }
});
