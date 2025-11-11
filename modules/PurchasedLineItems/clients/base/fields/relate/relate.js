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
 * @class View.Fields.Base.PurchasedLineItems.RelateField
 * @alias SUGAR.App.view.fields.BasePurchasedLineItemsRelateField
 * @extends View.Fields.Base.RelateField
 */
({
    extendsFrom: 'BaseRelateField',

    /**
     * @override
     *
     * Hiding the revenuelineitem_name field when the in Opps Only mode
     */
    _render: function() {
        var oppConfig = app.metadata.getModule('Opportunities', 'config');
        if (oppConfig && !_.isUndefined(this.def.showInMode) && oppConfig.opps_view_by !== this.def.showInMode) {
            this.$el.closest('.record-cell').hide();
        }
        this._super('_render');
    },
})
