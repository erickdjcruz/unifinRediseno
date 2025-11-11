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
 * @class View.Fields.Base.DiscountField
 * @alias SUGAR.App.view.fields.BaseDiscountField
 * @extends View.Fields.Base.CurrencyField
 */
({
    extendsFrom: 'CurrencyField',

    discountFieldName: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.discountFieldName = options.def.discountFieldName;

        // Store values for views that don't render twice.
        if (this.model.has('currency_id')) {
            this.lastCurrencyId = this.model.get('currency_id');
        }

        var numericValidationTaskName = 'isNumeric_validator_' + this.cid;

        // removing the validation task if it exists already for this field
        this.model.removeValidationTask(numericValidationTaskName);
        this.model.addValidationTask(numericValidationTaskName, _.bind(this._validateAsNumber, this));
    },

    /**
     * Callback for after validation runs.
     * @param {bool} isValid flag determining if the validation is correct
     * @private
     */
    _validationComplete: function(isValid) {
        if (isValid) {
            app.alert.dismiss('invalid-data');
        }
    },

    /**
     * @inheritdoc
     *
     * Listen for the discount_select field to change, when it does, re-render the field
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        // If discount select changes, we need to re-render this field
        this.model.on('change:' + this.discountFieldName, this.render, this);

        // If the discount type changes, we need to re-render this field
        this.model.on('change:currency_id', this.handleCurrencyFieldChange, this);
    },

    handleCurrencyFieldChange: function(model, currencyId, options) {
        if (!this.lastCurrencyId || !currencyId || _.property('revert')(options) === true) {
            this.lastCurrencyId = currencyId;
            return;
        }

        if (_.isEqual(this.model, model)) {
            // Should convert new discount_amount value if the new currency is different
            if (
                currencyId !== this.lastCurrencyId &&
                app.utils.isTruthy(model.get(this.discountFieldName)) === false
            ) {
                var convertedDiscountAmount = app.currency.convertAmount(
                    model.get(this.name),
                    this.lastCurrencyId,
                    currencyId
                );
                model.set(this.name, convertedDiscountAmount);
            }
            this.lastCurrencyId = currencyId;
        }
    },

    /**
     * @inheritdoc
     *
     * Special handling of the templates, if we are displaying it as a percent, then use the _super call,
     * otherwise get the templates from the currency field.
     */
    _loadTemplate: function() {
        if (app.utils.isTruthy(this.model.get(this.discountFieldName))) {
            this._super('_loadTemplate');
        } else {
            this._super('_loadTemplate');
            this.template = app.template.getField(
                'currency',
                this.action || this.view.action,
                this.module
            ) || app.template.empty;
            this.tplName = this.action || this.view.action;
        }
    },

    /**
     * @inheritdoc
     *
     * Special handling for the format, if we are in a percent, use the decimal field to handle the percent, otherwise
     * use the format according to the currency field
     */
    format: function(value) {
        if (app.utils.isTruthy(this.model.get(this.discountFieldName))) {
            return app.utils.formatNumberLocale(value);
        } else {
            //In edit mode hide the currency dropdown for the discount field
            this.hideCurrencyDropdown = this.tplName === 'edit' ? true : false;
            return this._super('format', [value]);
        }
    },

    /**
     * @inheritdoc
     *
     * Special handling for the unformat, if we are in a percent, use the decimal field to handle the percent,
     * otherwise use the format according to the currency field
     */
    unformat: function(value) {
        if (app.utils.isTruthy(this.model.get(this.discountFieldName))) {
            var unformattedValue = app.utils.unformatNumberStringLocale(value, true);
            // if unformat failed, return original value
            return _.isFinite(unformattedValue) ? unformattedValue : value;
        } else {
            return this._super('unformat', [value]);
        }
    },

    /**
     * Validate the discount field as a number - do not allow letters
     *
     * @param {Object} fields The list of field names and their definitions.
     * @param {Object} errors The list of field names and their errors.
     * @param {Function} callback Async.js waterfall callback.
     * @private
     */
    _validateAsNumber: function(fields, errors, callback) {
        var value = this.model.get(this.name);

        if (!_.isFinite(value)) {
            errors[this.name] = {'number': value};
        }

        callback(null, fields, errors);
    },

    /**
     * Extending to remove the custom validation task for this field
     *
     * @inheritdoc
     * @private
     */
    _dispose: function() {
        var numericValidationTaskName = 'isNumeric_validator_' + this.cid;
        this.model.removeValidationTask(numericValidationTaskName);

        this._super('_dispose');
    }
})
