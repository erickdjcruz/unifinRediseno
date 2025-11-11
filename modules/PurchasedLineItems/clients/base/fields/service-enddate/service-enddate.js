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
 * @class View.Fields.Base.PurchasedLineItems.ServiceEnddateField
 * @alias SUGAR.App.view.fields.BasePurchasedLineItemsServiceEnddateField
 * @extends View.Fields.Base.ServiceEnddateField
 */
({
    extendsFrom: 'BaseServiceEnddateField',

    /**
     * @override
     *
     * Override to calculate end date when the field is initialized. Because we
     * allow "goods" to have a service end date on PLIs, we need to calculate as
     * soon as the create drawer opens to avoid ever having a null end date.
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        if (this.view.action === 'create') {
            this.calculateEndDate();
        }
    },

    /**
     * @override
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.model.on('change:purchase_name', this.calculateEndDate, this);
    },

    /**
     * @override
     *
     * Overrides calculate end date function of base service end date field,
     * since we do have a desired end date for non-service PLIs
     */
    calculateEndDate: function() {
        if (this.model.get('service')) {
            this._super('calculateEndDate');
        } else {
            this.model.set(this.name, this.model.get('service_start_date'));
        }
    }
})
