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
 * @class View.Fields.Base.PurchasedLineItems.CreateAddOnField
 * @alias SUGAR.App.view.fields.BasePurchasedLineItemsCreateAddOnField
 * @extends View.Fields.Base.RowactionField
 */
({
    extendsFrom: 'RowactionField',
    events: {
        'click [data-action=existing_opportunity]': 'existingOpportunityClicked',
        'click [data-action=new_opportunity]': 'newOpportunityClicked',
        'click [data-action=link]': 'toggleAddOns'
    },

    _render: function() {
        if (this.parent && this.parent.model && this.parent.model.get('service')) {
            this._super('_render');
            this.$('[data-action=existing_opportunity]').hide();
            this.$('[data-action=new_opportunity]').hide();
            this.$('.dropdown-inset').hide();
        }
    },

    /**
     * Toggles new and existing opportunity buttons
     * @param {Event} evt
     */
    toggleAddOns: function(evt) {
        if (evt) {
            evt.preventDefault();
            evt.stopPropagation();
        }
        this.$('[data-action=existing_opportunity]').toggle();
        this.$('[data-action=new_opportunity]').toggle();
        this.$('.dropdown-inset').toggle();
    },

    /**
     * Handles Existing Opportunity button being clicked
     * @param {Event} evt
     */
    existingOpportunityClicked: function(evt) {
        var parentModel = this.parent.model;
        var filterOptions = new app.utils.FilterOptions()
            .config({
                initial_filter: 'filterOpportunityTemplate',
                initial_filter_label: 'LBL_FILTER_OPPORTUNITY_TEMPLATE',
                filter_populate: {
                    account_id: parentModel.get('account_id')
                }
            }).format();

        // Open existing opportunities
        app.drawer.open({
            layout: 'selection-list',
            context: {
                module: 'Opportunities',
                filterOptions: filterOptions,
                parent: this.context,
            }
        }, _.bind(this.selectExistingOpportunityDrawerCallback, this));
    },

    /**
     * Handles New Opportunity button being clicked
     * @param {Event} evt
     */
    newOpportunityClicked: function(evt) {
        var pliModel = this.parent.model;

        // Set the values for the new Opportunity
        var opportunityModel = app.data.createBean('Opportunities');
        opportunityModel.set({
            account_id: pliModel.get('account_id'),
            account_name: pliModel.get('account_name'),
        });

        // Set the basic values for the new RLI
        var addOnToData = {
            add_on_to_id: pliModel.get('id'),
            add_on_to_name: pliModel.get('name'),
            service: '1'
        };

        var self = this;
        this._getAddOnRelatedFieldValues(pliModel, addOnToData, function(rliData) {
            app.drawer.open({
                layout: 'create',
                context: {
                    create: true,
                    module: 'Opportunities',
                    model: opportunityModel,
                    addOnToData: rliData
                }
            },  _.bind(self.refreshRLISubpanel, self));
        });
    },

    /**
     * Open new RevenueLineItem drawer when an opportunity is selected
     * @param {Object} model
     */
    selectExistingOpportunityDrawerCallback: function(model) {
        if (!model || _.isEmpty(model.id)) {
            return;
        }
        var revenueLineItemModel = app.data.createBean('RevenueLineItems');
        var pliModel = this.parent.model;
        // set up RLI to open when opportunity is selected
        var addOnToData = {
            add_on_to_id: pliModel.get('id'),
            add_on_to_name: pliModel.get('name'),
            service: '1',
            opportunity_name: model.name,
            opportunity_id: model.id
        };

        var self = this;
        this._getAddOnRelatedFieldValues(pliModel, addOnToData, function(rliData) {
            revenueLineItemModel.set(rliData);
            app.drawer.open({
                layout: 'create',
                context: {
                    create: true,
                    module: 'RevenueLineItems',
                    model: revenueLineItemModel
                }
            }, _.bind(self.refreshRLISubpanel, self));
        });
    },

    /**
     * Retrieves data from a PLI model and its Product Template if applicable.
     * Used by "Add On To" fields to populate default values from multiple sources
     * based on the value of the "Add On To" field
     *
     * @param pliModel the PLI model
     * @param addOnToData the object holding attributes related to the "Add On To" field
     * @param callback the callback function to call when data is finished being retrieved
     * @private
     */
    _getAddOnRelatedFieldValues: function(pliModel, addOnToData, callback) {
        // Get the values to include on the RLI based on the PLI and/or its related
        // Product Template
        var rliFields = app.metadata.getModule('RevenueLineItems', 'fields');
        if (rliFields && rliFields.add_on_to_name && !_.isEmpty(rliFields.add_on_to_name.copyFromPurchasedLineItem)) {
            _.each(rliFields.add_on_to_name.copyFromPurchasedLineItem, function(fromField, toField) {
                if (_.isEmpty(addOnToData[toField])) {
                    addOnToData[toField] = pliModel.get(fromField);
                }
            }, this);
        }
        if (rliFields && rliFields.add_on_to_name && !_.isEmpty(rliFields.add_on_to_name.copyFromProductTemplate) &&
            addOnToData.product_template_id) {
            // The PLI is using a product template, and there are fields to copy
            // from it, so fetch its data before opening the create drawer
            var productTemplateBean = app.data.createBean('ProductTemplates', {id: addOnToData.product_template_id});
            app.alert.show('fetching_product_template', {
                level: 'process',
                title: app.lang.get('LBL_LOADING'),
                autoClose: false
            });
            productTemplateBean.fetch({
                success: _.bind(function(templateData) {
                    _.each(rliFields.add_on_to_name.copyFromProductTemplate, function(toField, fromField) {
                        if (_.isEmpty(addOnToData[toField])) {
                            addOnToData[toField] = templateData.get(fromField);
                        }
                    }, this);
                }, this),
                complete: _.bind(function() {
                    app.alert.dismiss('fetching_product_template');
                    callback(addOnToData);
                }, this)
            });
        } else {
            // The PLI is not using a product template, or there are no fields to
            // copy from it, so just open the create drawer
            callback(addOnToData);
        }
    },

    refreshRLISubpanel: function(model) {
        if (!model) {
            return;
        }
        var ctx = this.listContext || this.context;
        ctx.reloadData({recursive: false});
        // Refresh RevenueLineItems subpanel when drawer is closed
        if (!_.isUndefined(ctx.children)) {
            _.each(ctx.children, function(child) {
                if (_.contains(['RevenueLineItems'], child.get('module'))) {
                    child.reloadData({recursive: false});
                }
            });
        }
    },

    /**
     * @inheritdoc
     * Check access.
     */
    hasAccess: function() {
        var pliViewAccess = app.acl.hasAccess('view', 'PurchasedLineItems');
        var rliCreateAccess = app.acl.hasAccess('create', 'RevenueLineItems');
        var oppCreateAccess = app.acl.hasAccess('create', 'Opportunities');
        var oppConfig = app.metadata.getModule('Opportunities', 'config');
        var rlisTurnedOn = oppConfig && oppConfig.opps_view_by === 'RevenueLineItems';
        return pliViewAccess && rliCreateAccess &&
            oppCreateAccess && rlisTurnedOn && this._super('hasAccess');
    }
})
