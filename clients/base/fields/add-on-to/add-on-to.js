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
 * @class View.Fields.Base.AddOnToField
 * @alias SUGAR.App.view.fields.BaseAddOnToField
 * @extends View.Fields.Base.RelateField
 */
({
    extendsFrom: 'RelateField',

    /**
     * Gets the names of fields to include from the related record when fetching
     * data. Overridden to ensure that the fields defined in the
     * 'copyFromPurchasedLineItem' property of field vardefs are included in the
     * fetch
     *
     * @return {Array} the array of related field names to include in the search
     *                 query
     */
    getSearchFields: function() {
        var searchFields = this._super('getSearchFields');
        return _.union(searchFields, _.keys(this.def.copyFromPurchasedLineItem || {}));
    },

    /**
     * Formats the filter options for add_on_to_name field.
     *
     * @param {boolean} force `true` to force retrieving the filter options
     *                  whether or not it is available in memory.
     * @return {Object} The filter options.
     */
    getFilterOptions: function(force) {
        if (this.model && !_.isEmpty(this.model.get('account_id'))) {
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

    /**
     * Overrides the parent updateRelatedFields so that when the "Add On To"
     * field is changed, we can import values from both the related PLI and
     * the related-related Product Template
     *
     * @param {Object} relatedAttributes the attributes of the related record
     */
    updateRelatedFields: function(relatedAttributes) {
        this._super('updateRelatedFields', [relatedAttributes]);
        this._updateAddOnToRelatedFields(relatedAttributes);
    },

    /**
     * Sets values on the model from both the related PLI as well as the
     * related-related Product Template.
     *
     * To specify the field value mappings from the related PLI, you can modify
     * the 'copyFromPurchasedLineItem' property of the field vardefs
     *
     * To specify the field value mappings from the related-related Product Template,
     * you can modify the 'copyFromPurchasedLineItem' property of the field vardefs
     *
     * @param {Object} relatedAttributes the attributes of the related record
     */
    _updateAddOnToRelatedFields: function(relatedAttributes) {
        relatedAttributes = relatedAttributes || {};
        var attrs = {};

        // Copy fields from the related PLI
        if (this.def && this.def.copyFromPurchasedLineItem) {
            _.each(this.def.copyFromPurchasedLineItem, function(fromField, toField) {
                if (relatedAttributes[fromField] !== attrs[toField]) {
                    attrs[toField] = relatedAttributes[fromField];
                }
            }, this);
        }

        // Copy fields from the related-related Product Template
        if (this.def && this.def.copyFromProductTemplate && !_.isEmpty(attrs.product_template_id)) {
            var productTemplateBean = app.data.createBean('ProductTemplates', {id: attrs.product_template_id});
            app.alert.show('fetching_product_template', {
                level: 'process',
                title: app.lang.get('LBL_LOADING'),
                autoClose: false
            });
            productTemplateBean.fetch({
                success: _.bind(function(templateData) {
                    _.each(this.def.copyFromProductTemplate, function(toField, fromField) {
                        var fromValue = templateData.get(fromField);
                        if (fromValue !== this.model.get(toField)) {
                            attrs[toField] = fromValue;
                        }
                    }, this);
                    this.model.set(attrs);
                    this.model.trigger('addon:pli:changed');
                }, this),
                complete: function() {
                    app.alert.dismiss('fetching_product_template');
                }
            });
        } else {
            this.model.set(attrs);
            this.model.trigger('addon:pli:changed');
        }
    },

    /**
     * Extends the parent _loadTemplate to ensure that this field uses the relate
     * field templates
     */
    _loadTemplate: function() {
        this.type = 'relate';
        this._super('_loadTemplate');
        this.type = this.def.type;
    },
})
