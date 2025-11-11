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
 * @class View.Fields.Base.Metrics.FreezeFirstColumnField
 * @alias SUGAR.App.view.fields.BaseMetricsFreezeFirstColumnField
 * @extends View.Fields.Base.BoolField
 */
({
    extendsFrom: 'BoolField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.setupField();
    },

    /**
     * Set the field value on load to be checked/unchecked based on the saved config
     */
    setupField: function() {
        let freezeFirstColumn = this.model.get('freeze_first_column');
        if (_.isUndefined(freezeFirstColumn)) {
            this.model.set('freeze_first_column', true);
            this.value = true;
        }
    }
})
