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
 * @class View.Views.Base.PurchasedLineItems.CreateView
 * @alias SUGAR.App.view.views.PurchasedLineItemsCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * Adds PurchaseAndServiceChangeHandler plugin
     * Manually sets default values for service related field
     *
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        // Adds this plugin to handle changes to Service and Purchase Name fields
        this.plugins = _.union(this.plugins || [], ['PurchaseAndServiceChangeHandler']);
        this._super('initialize', [options]);

        if (!_.isUndefined(this.model)) {
            var parent = !_.isUndefined(this.context.parent) ? this.context.parent : {};
            var parentModule = !_.isEmpty(parent) ? parent.get('module') : '';

            // If the create view drawer is opened from the Purchase module PLI subpanel
            // and Purchase and Product name are already populated
            if (parentModule === 'Purchases' && !_.isEmpty(this.model.get('purchase_name')) &&
                !_.isEmpty(this.model.get('product_template_id'))) {
                this.handlePurchaseChange();
            }

            // Setting service_duration_value field defaults here since it is coming from the sales_item vardefs
            this.model.setDefault('service_duration_value', 1);

            var modelFields = this.model.fields || {};
            if (!_.isEmpty(modelFields) && !_.isUndefined(modelFields.service_start_date)) {
                // Service start date displays today's date by default
                modelFields.service_start_date.display_default = 'now';
            }

            // This sets the service_duration_unit based on service field on first load
            this.handleServiceChange();
            // Binds handlers for service and purchase_name changes
            this.bindDataChange();
        }
    },
})
