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
 * Custom Subpanel Layout for Revenue Line Items.
 *
 * @class View.Views.Base.RevenueLineItems.SubpanelForOpportunitiesCreate
 * @alias SUGAR.App.view.views.BaseRevenueLineItemsSubpanelForOpportunitiesCreate
 * @extends View.Views.Base.SubpanelListCreateView
 */
({
    extendsFrom: 'SubpanelListCreateView',

    /**
     * List of field names that can cascade down from the Opp level
     * @property {Array}
     */
    cascadableFields: [
        'service_duration_value',
        'service_duration_unit',
        'service_start_date',
        'date_closed',
        'sales_stage',
        'commit_stage',
    ],

    /**
     * List of field names that can cascade down from the Opp level, as they're used in the viewdefs.
     * This differs from the above list as it does not have fieldset's subfields
     * @property {Array}
     */
    cascadableViewFields: [
        'service_duration',
        'service_start_date',
        'date_closed',
        'sales_stage',
        'commit_stage',
    ],

    /**
     * List of field names that, when changed, can allow or disallow cascading
     * @property {Array}
     */
    cascadePrereqFields: ['service', 'add_on_to_id', 'lock_duration'],

    initialize: function(options) {
        // From SS-492: This allows the RLI subpanel on Opportunities/create to pick up the layout from the out-of-the-
        // box RLI subpanel in Studio. It swaps the metadata, and then initializes the RLI subpanel on
        // Opportunities/create with the new metadata. It will only use the RLI subpanel metadata if it exists;
        // otherwise it'll use the metadata found in this folder (in the associated php file). Since creating an
        // Opportunity does not make any requests to the server, this functionality needs to take place in the client.
        var subpanelLayouts = app.metadata.getModule('Opportunities').layouts.subpanels.meta.components;
        var rliSubpanelLayout = _.chain(subpanelLayouts)
            .filter(function(e) {
                return e.context.link === 'revenuelineitems';
            })
            .first()
            .value();
        var rliSubpanelViewName = _.property('override_subpanel_list_view')(rliSubpanelLayout);
        var rliModuleViews = app.metadata.getModule('RevenueLineItems').views;

        if (!_.isEmpty(rliSubpanelViewName)) {
            var customRliSubpanelViewDefs = _.property(rliSubpanelViewName)(rliModuleViews);

            if (!_.isEmpty(customRliSubpanelViewDefs)) {
                var subpanelFields = _.first(customRliSubpanelViewDefs.meta.panels).fields;
                _.first(options.meta.panels).fields = subpanelFields;
            }
        }

        this._super('initialize', [options]);
    },

    /**
     * Add any custom or default fields to the bean, including Opp-level cascade fields
     * @inheritdoc
     */
    _addCustomFieldsToBean: function(bean, skipCurrency, prepopulatedData) {
        var dom;
        var attrs = {};
        var userCurrencyId;
        var userCurrency = app.user.getCurrency();
        var createInPreferred = userCurrency.currency_create_in_preferred;
        var currencyFields;
        var currencyFromRate;

        if (bean.has('sales_stage')) {
            dom = app.lang.getAppListStrings('sales_probability_dom');
            attrs.probability = dom[bean.get('sales_stage')];
        }

        if (skipCurrency && createInPreferred) {
            // force the line item to the user's preferred currency and rate
            attrs.currency_id = userCurrency.currency_id;
            attrs.base_rate = userCurrency.currency_rate;

            // get any currency fields on the model
            currencyFields = _.filter(this.model.fields, function(field) {
                return field.type === 'currency';
            });
            currencyFromRate = bean.get('base_rate');

            _.each(currencyFields, function(field) {
                // if the field exists on the bean, convert the value to the new rate
                // do not convert any base currency "_usdollar" fields
                if (bean.has(field.name) && field.name.indexOf('_usdollar') === -1) {
                    attrs[field.name] = app.currency.convertWithRate(
                        bean.get(field.name),
                        currencyFromRate,
                        userCurrency.currency_rate
                    );
                }
            }, this);
        } else if (!skipCurrency) {
            userCurrencyId = userCurrency.currency_id || app.currency.getBaseCurrencyId();
            attrs.currency_id = userCurrencyId;
            attrs.base_rate = app.metadata.getCurrency(userCurrencyId).conversion_rate;
        }
        attrs.catalog_service_duration_value = bean.get('service_duration_value');
        attrs.catalog_service_duration_unit = bean.get('service_duration_unit');

        var addOnToData = this.context.parent.get('addOnToData');
        if (addOnToData) {
            _.each(addOnToData, function(value, key) {
                attrs[key] = value;
            }, this);
            this.context.parent.set('addOnToData', null);
        }

        // If any Opp-level cascade fields are set, include those as well
        let cascadeFields = this.getCascadeFieldsFromOpp(bean, attrs);
        let oppModel = this._getOppModel();
        if (oppModel) {
            Object.entries(cascadeFields).forEach(([fieldName, fieldValue]) => {
                let cascadeChecked = oppModel.get(`${this._getParentFieldName(fieldName)}_cascade_checked`);
                if (app.utils.isTruthy(cascadeChecked) && app.utils.isRliFieldValidForCascade(bean, fieldName, attrs)) {
                    attrs[fieldName] = fieldValue;
                }
            });
        }

        if (!_.isEmpty(prepopulatedData) && !_.isUndefined(prepopulatedData.lock_duration)) {
            attrs.lock_duration = prepopulatedData.lock_duration;
        }

        if (!_.isEmpty(attrs)) {
            // we need to set the defaults
            bean.setDefault(attrs);
            // just to make sure that any attributes that were already set, are set again.
            bean.set(attrs);
        }

        // Fix the forecast field. If sales_stage and commit_stage are both cascaded at once, then commit_stage
        // gets recalculated, ignoring the cascade-set value.
        if (_.has(cascadeFields, 'commit_stage') && !_.isEmpty(cascadeFields.commit_stage)) {
            let forecastCascadeChecked = app.utils.isTruthy(oppModel.get('commit_stage_cascade_checked'));
            if (forecastCascadeChecked && app.utils.isRliFieldValidForCascade(bean, 'commit_stage')) {
                bean.setDefault('commit_stage', cascadeFields.commit_stage);
                bean.set('commit_stage', cascadeFields.commit_stage);
            }
        }

        return bean;
    },

    /**
     * Gets the model of the parent Opp, if one exists
     * @return {null|*}
     * @private
     */
    _getOppModel: function() {
        if (!this.context || !this.context.parent) {
            return null;
        }
        return this.context.parent.get('model');
    },

    /**
     * Gets the current values of the Opp level cascade fields
     * @param bean
     * @param attrs (optional)
     * @return {Object}
     */
    getCascadeFieldsFromOpp: function(bean, attrs) {
        let oppModel = this._getOppModel();
        if (!oppModel) {
            return {};
        }

        let cascadeValues = {};
        this.cascadableFields.forEach(fieldName => {
            let cascadeFieldName = fieldName + '_cascade';

            if (app.utils.isRliFieldValidForCascade(bean, fieldName, attrs)) {
                cascadeValues[fieldName] = oppModel.get(cascadeFieldName) || '';
            } else {
                cascadeValues[fieldName] = '';
            }
        });

        return cascadeValues;
    },

    /**
     * Add listeners to the bean to ensure cascadable fields are set properly when prereq fields are changed.
     * @inheritdoc
     */
    _addCustomEventHandlers: function(bean) {
        this.cascadePrereqFields.forEach(fieldName => {
            bean.on('change:' + fieldName, this.checkCascadePrereqChanges, this);
        });
        this.cascadableFields.forEach(fieldName => {
            bean.on('change:' + fieldName, this.verifyChangeFromCascade, this);
        });
    },

    /**
     * Handles changes to fields that cascadable fields depend on. When a prereq field changes, update any
     * fields that depend on it
     * @param model
     */
    checkCascadePrereqChanges: function(model) {
        let oppModel = this._getOppModel();
        if (!oppModel) {
            return;
        }
        let cascadeFields = this.getCascadeFieldsFromOpp(model);

        Object.entries(cascadeFields).forEach(([fieldName, fieldValue]) => {
            let cascadeChecked = oppModel.get(this._getParentFieldName(fieldName) + '_cascade_checked');
            if (app.utils.isTruthy(cascadeChecked) && app.utils.isRliFieldValidForCascade(model, fieldName)) {
                model.set(fieldName, fieldValue);
            }
        });

        this._checkCascadeFieldEditability();
    },

    /**
     * When one of the cascadable fields is modified, make sure that it inherits the cascade value
     * if applicable. This is to ensure the cascading works properly with SetValue actions.
     * @param model
     */
    verifyChangeFromCascade: function(model) {
        let oppModel = this._getOppModel();
        if (!oppModel) {
            return;
        }

        this.cascadableFields.forEach(fieldName => {
            let cascadeChecked = oppModel.get(this._getParentFieldName(fieldName) + '_cascade_checked');
            if (app.utils.isTruthy(cascadeChecked) && app.utils.isRliFieldValidForCascade(model, fieldName)) {
                model.set(fieldName, oppModel.get(fieldName + '_cascade'));
            }
        });
    },

    /**
     * Gets the name of the parent field. For most fields this will be the field name, but for a subfield
     * in a fieldset, it is the name of the fieldset.
     * @param fieldName
     * @return {string}
     * @private
     */
    _getParentFieldName: function(fieldName) {
        if (['service_duration_unit', 'service_duration_value'].includes(fieldName)) {
            return 'service_duration';
        }
        return fieldName;
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');
        this._checkCascadeFieldEditability();
    },

    /**
     * Check the parent Opp model for if we need to enable or disable cascadable fields
     * @private
     */
    _checkCascadeFieldEditability: function() {
        let oppModel = this._getOppModel();
        if (!oppModel) {
            return;
        }

        this.cascadableViewFields.forEach(fieldName => {
            if (app.utils.isTruthy(oppModel.get(fieldName + '_cascade_checked'))) {
                oppModel.trigger('cascade:checked:' + fieldName, true);
            }
        });
    },

    /**
     * We have to overwrite this method completely, since there is currently no way to completely disable
     * a field from being displayed
     *
     * @returns {{default: Array, available: Array, visible: Array, options: Array}}
     */
    parseFields : function() {
        var catalog = this._super('parseFields');
        var forecastConfig = app.metadata.getModule('Forecasts', 'config');

        // if forecast is not setup, we need to make sure that we hide the commit_stage field
        _.each(catalog, function (group, i) {
            var filterMethod = _.isArray(group) ? 'filter' : 'pick';
            if (forecastConfig && forecastConfig.is_setup) {
                catalog[i] = _[filterMethod](group, function(fieldMeta) {
                    if (fieldMeta.name.indexOf('_case') != -1) {
                        var field = 'show_worksheet_' + fieldMeta.name.replace('_case', '');
                        return (forecastConfig[field] == 1);
                    }

                    return true;
                });
            } else {
                catalog[i] = _[filterMethod](group, function(fieldMeta) {
                    return (fieldMeta.name != 'commit_stage');
                });
            }
        });

        return catalog;
    }
})
