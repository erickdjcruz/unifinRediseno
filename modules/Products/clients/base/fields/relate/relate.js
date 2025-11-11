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
 * @class View.Fields.Base.Products.RelateField
 * @alias SUGAR.App.view.fields.BaseProductsRelateField
 * @extends View.Fields.Base.RelateField
 */
({
    extendsFrom: 'BaseRelateField',

    /**
     * Formats the filter options for add_on_to_name field.
     *
     * @param {boolean} force `true` to force retrieving the filter options whether or not it is available in memory.
     * @return {Object} The filter options.
     */
    getFilterOptions: function(force) {
        if (this.name && this.name === 'add_on_to_name' &&
            this.model && !_.isEmpty(this.model.get('account_id'))) {
            return new app.utils.FilterOptions()
                .config({
                    'initial_filter': 'add_on_plis',
                    'initial_filter_label': 'LBL_PLI_ADDONS',
                    'filter_populate': {
                        'account_id': [this.model.get('account_id')]
                    },
                })
                .format();
        } else {
            return this._super('getFilterOptions', [force]);
        }
    },
})
