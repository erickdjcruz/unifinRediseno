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
 * @class View.Fields.Base.ArchiveRunsFilterdefField
 * @alias SUGAR.App.view.fields.BaseArchiveRunsFilterdefField
 * @extends View.Fields.Base.FilterDefField
 */
({
    /**
     * @param {Object} options
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // For this instance of the filter-def field, we want the module used to be the source_module field
        this.module = this.model.get('source_module');
    }
})
