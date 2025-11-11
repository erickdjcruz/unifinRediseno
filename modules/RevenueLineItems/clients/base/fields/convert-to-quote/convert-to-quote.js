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
 * @class View.Fields.Base.RevenueLineItems.ConvertToQuoteField
 * @alias SUGAR.App.view.fields.BaseRevenueLineItemsConvertToQuoteField
 * @extends View.Fields.Base.RowactionField
 */
({
    extendsFrom: 'RowactionField',

    /**
     * @inheritdoc
     *
     * @param {Object} options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'rowaction';

        this.context.on('button:convert_to_quote:click', this.convertToQuote, this);
    },

    /**
     * convert RLI to quote
     * @param {Object} e
     */
    convertToQuote: function(e) {
        var massCollection = this.context.get('mass_collection');
        if (!massCollection) {
            massCollection = this.context.get('collection').clone();
            this.context.set('mass_collection', massCollection);
        }

        this.view.layout.trigger('list:massquote:fire');
    },

    /**
     * @inheritdoc
     */
    isAllowedDropdownButton: function() {
        // Filter logic for when it's on a dashlet
        return this.view.name !== 'dashlet-toolbar';
    }
})
