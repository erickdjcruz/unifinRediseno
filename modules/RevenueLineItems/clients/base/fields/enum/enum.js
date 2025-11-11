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
 * @class View.Fields.Base.RevenueLineItems.EnumField
 * @alias SUGAR.App.view.fields.BaseRevenueLineItemsEnumField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * List of valid cascadable fields of this type
     * @property {Array}
     */
    cascadableFields: ['sales_stage', 'commit_stage'],

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        if (this.view.name === 'subpanel-for-opportunities-create' && this.cascadableFields.includes(this.name)) {
            let oppModel = this.context.parent.get('model');
            oppModel.on('cascade:checked:' + this.name, function(checked) {
                if (this.disposed || !app.utils.isRliFieldValidForCascade(this.model, this.name)) {
                    return;
                }
                this.setDisabled(checked, {trigger: true});
            }, this);

            this.context.on('field:disabled', function(fieldName) {
                if (this.name === fieldName) {
                    let oppModel = this.context.parent.get('model');
                    if (app.utils.isTruthy(oppModel.get(this.name + '_cascade_checked'))) {
                        this.setDisabled(true);
                    }
                }
            }, this);
        }
    }
})
