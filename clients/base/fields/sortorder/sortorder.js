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
 * Sort Order Widget.
 *
 * This allows an user to select the sorting order
 *
 * @class View.Fields.Base.SortorderField
 * @alias SUGAR.App.view.fields.BaseSortorderField
 * @extends View.Fields.Base.Field
 */
({
    events: {
        'click [data-action=order-switcher]': 'switchOrder',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // setup the default value
        if (!this.model.get(this.name)) {
            this.model.set(this.name, this.def.default);
        }
    },

    /**
     * Update the order by column
     *
     * @param {jQuery} e
     */
    switchOrder: function(e) {
        this.model.set(this.name, e.currentTarget.value);
    },
})
