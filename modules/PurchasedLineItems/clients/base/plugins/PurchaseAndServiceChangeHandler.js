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
(function(app) {
    app.events.on('app:init', function() {

        /**
         * This plugin is built to share the service and purchase name handlers among views
         */
        app.plugins.register('PurchaseAndServiceChangeHandler', ['view'], {
            /**
             * @inheritdoc
             *
             * Listen for the purchase_name and service fields to change
             * This takes care of the case when Purchase name is changed on record view in edit mode
             */
            bindDataChange: function() {
                this._super('bindDataChange');

                // Bind handlers for service and purchase_name change events
                this.model.on('change:service', this.handleServiceChange, this);
                this.model.on('change:purchase_name', this.handlePurchaseChange, this);
            },

            /**
             * Handler for Service change event
             * update Service Duration, Start Date, and End Date
             */
            handleServiceChange: function() {
                this._updateServiceDuration();
                this._updateStartEndDate();
            },

            /**
             * Switches between service duration unit as 'Year(s)' or 'Day(s)' based on service
             * field. If the unit is already set, we should allow the existing enum handler to
             * update that property for services.
             *
             * For non-services, we set the duration to 1 and the unit to day
             */
            _updateServiceDuration: function() {
                var service = this.model.get('service');

                if (service && _.isEmpty(this.model.get('service_duration_unit'))) {
                    this.model.set('service_duration_unit', 'year');
                } else if (!service) {
                    this.model.set({
                        'service_duration_unit': 'day',
                        'service_duration_value': 1
                    });
                }
            },

            /**
             * Updates start and end dates based on the 'service' field changing
             * For services, start date should stay whatever it was before, and
             * end date should be re-calculated
             *
             * For goods, Start and End date should be set to the expected close
             * date
             */
            _updateStartEndDate: function() {
                if (this.model.get('service')) {
                    var endDate = this.getField('service_end_date');
                    if (!_.isUndefined(endDate)) {
                        endDate.calculateEndDate();
                    }
                } else {
                    var closeDate = this.model.get('date_closed');
                    this.model.set({
                        'service_start_date': closeDate,
                        'service_end_date': closeDate
                    });
                }
            },

            /**
             * Handler for purchase_name change event
             * Gets the Product populate_list fields based on the Product_id coming from the Purchase
             */
            handlePurchaseChange: function() {
                // checks if model is defined and has purchase defined in it
                var purchase = !_.isUndefined(this.model.get('purchase')) ? this.model.get('purchase') : {};
                var prodTemplateId = purchase.product_template_id || '';

                // this call the method for api call only if prodTemplateId is defined
                if (!_.isEmpty(prodTemplateId)) {
                    var modelFields = this.model.fields || {};
                    var populateList = {};
                    if (!_.isUndefined(modelFields.product_template_name) &&
                        !_.isUndefined(modelFields.product_template_name.populate_list)) {
                        populateList = modelFields.product_template_name.populate_list;
                    }

                    this.setProductAutoPopulateFields(prodTemplateId, populateList);
                }
            },

            /**
             * Makes and api call with the given product_template_id and then set the values from the response
             * for the fields in populate_list object
             *
             * @param string prodTemplateId id for the product_template to be fetched
             * @param object populateList List of fields to be populated from the fetched product_template
             */
            setProductAutoPopulateFields: function(prodTemplateId, populateList) {
                if (!_.isEmpty(prodTemplateId)) {
                    app.api.call('read', app.api.buildURL('ProductTemplates/' + prodTemplateId),
                        {}, {
                            success: _.bind(function(data) {
                                var productPopulateList =
                                    _.pick(data, _.keys(populateList));
                                if (!_.isUndefined(productPopulateList.name) &&
                                    !_.isEmpty(this.model.get('name'))) {
                                    delete productPopulateList.name;
                                }
                                this.model.set(productPopulateList);

                                // if the created PLI is not a service reset duration to "1 Day(s)"
                                if (this.model.get('service') !== true) {
                                    this.model.set('service_duration_unit', 'day');
                                    this.model.set('service_duration_value', '1');
                                }

                                // Always store the service duration values from the product template
                                this.model.set('catalog_service_duration_value', data.service_duration_value);
                                this.model.set('catalog_service_duration_unit', data.service_duration_unit);

                                // when adding additional items to the list, causing additional renders,
                                // this.changed gets set undefined on re-initialize, so we need to make sure
                                // if this is an unsaved model and this.changed is undefined, that we set changed true
                                this.changed = true;
                            }, this)
                        }
                    );
                }
            }
        });
    });
})(SUGAR.App);
