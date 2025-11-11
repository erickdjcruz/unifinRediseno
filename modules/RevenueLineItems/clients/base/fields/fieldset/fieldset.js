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
 * @class View.Fields.Base.RevenueLineItems.FieldsetField
 * @alias SUGAR.App.view.fields.BaseRevenueLineItemsFieldsetField
 * @extends View.Fields.Base.FieldsetField
 */
({
    extendsFrom: 'FieldsetField',

    /**
     * List of valid cascadable fields of this type
     * @property {Array}
     */
    cascadableFields: ['service_duration'],

    /**
     * Cascadable fieldsets with subfields
     * @property {Object}
     */
    cascadableSubfields: {
        'service_duration': ['service_duration_unit', 'service_duration_value']
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        if (this.view.name === 'subpanel-for-opportunities-create' && this.cascadableFields.includes(this.name)) {
            let oppModel = this.context.parent.get('model');
            oppModel.on('cascade:checked:' + this.name, function(checked) {
                if (this.disposed) {
                    return;
                }
                if (!this.cascadableSubfields[this.name].every(subfield => {
                    return app.utils.isRliFieldValidForCascade(this.model, subfield);
                })) {
                    return;
                }
                this.setDisabled(checked, {trigger: true});
            }, this);

            this.context.on('field:disabled', function(fieldName) {
                Object.entries(this.cascadableSubfields).forEach(([field, subfields]) => {
                    if (subfields.includes(fieldName) && field === this.name) {
                        let oppModel = this.context.parent.get('model');
                        if (app.utils.isTruthy(oppModel.get(this.name + '_cascade_checked'))) {
                            this.setDisabled(true);
                        }
                    }
                });
            }, this);
        }
    }
})
