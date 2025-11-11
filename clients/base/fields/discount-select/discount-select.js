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
 * @class View.Fields.Base.DiscountSelectField
 * @alias SUGAR.App.view.fields.BaseDiscountSelectField
 * @extends View.Fields.Base.ActiondropdownField
 */
({
    extendsFrom: 'EnumField',

    /**
     * The class added to the select2 container.
     */
    containerCssClass: 'discount-select-dropdown',

    /**
     * The current currency object.
     */
    currentCurrency: null,

    /**
     * The current symbol to use in place of the caret dropdown icon
     */
    currentDropdownSymbol: null,

    /**
     * The key,value pairs available in the dropdown.
     */
    items: null,

    /**
     * The model property we are updating.
     */
    name: 'discount_select',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.lastDiscountSelectValue = this.model.get(this.name);

        if (this.view && this.view.layout) {
            this.view.layout.on('headerpane:adjust_fields', this.repositionDropdown, this);
        }

        // Need to call these for the times where the model is fully populated and we don't need
        // to wait for a second render to display the correct data.
        if (this.model.has('currency_id')) {
            this.fetchCurrency();
            this.loadEnumOptions();
            this.updateDropdownSymbol(this.model.get(this.name));
            this.updateDropdownText(this.currentDropdownSymbol);
        }
    },

    /**
     * Helper function for generating Select2 options for this enum
     * @param {Array} optionsKeys Set of option keys that will be loaded into Select2 widget
     * @return {Object} Select2 options, refer to Select2 documentation for what each option means
     * @override
     */
    getSelect2Options: function(optionsKeys) {
        var options = this._super('getSelect2Options', [optionsKeys]);

        // Updated options for our dropdown
        var newOptions = {
            placeholder: this.currentDropdownSymbol,
            containerCssClass: this.containerCssClass,
            dropdownCss: {width: 'auto'},
            width: '28px',
        };

        return _.extendOwn(options, newOptions);
    },

    /**
     * @override
     */
    bindDataChange: function() {
        // Handle discount_select update
        this.model.on('change:' + this.name, this.handleDiscountSelectFieldChange, this);

        // If the currency is changed in another field, updated our enum menu
        this.model.on('change:currency_id', this.handleCurrencyFieldChange, this);
    },

    /**
     * Handles updating the discount_select property and reflecting the correct currency symbol
     * in the dropdown.
     *
     * @param model The model of the row that was changed
     * @param discountSelect The new value for the discount_select field
     * @param options The on-change options returned from actions
     */
    handleDiscountSelectFieldChange: function(model, discountSelect, options) {
        if (_.property('revert')(options) === true) {
            this.updateDropdownSymbol(discountSelect);
            this.updateDropdownText(this.currentDropdownSymbol);
            return;
        }

        if (_.isEqual(this.model, model)) {
            // Need to update the model property to be a boolean. The saved value is a boolean
            // but the value from the enum field items is a string. We need to handle both so
            // for expected behaviour.
            var updatedValue = app.utils.isTruthy(discountSelect);
            model.set(this.name, updatedValue);

            this.$(this.fieldTag).select2('val', updatedValue);

            if (this.currentCurrency) {
                this.updateDropdownSymbol(updatedValue);
                this.updateDropdownText(this.currentDropdownSymbol);
            }
        }
    },

    /**
     * Handles updating the currency symbol in the dropdown and updating the enum options if
     * the currency is changed while the record is being edited.
     *
     * @param model The model of the row that has changed
     * @param currencyId The new value for our currency_id field
     * @param options The on-change options returned from actions
     */
    handleCurrencyFieldChange: function(model, currencyId, options) {
        if (_.property('revert')(options) === true) {
            this.fetchCurrency();
            this.loadEnumOptions();
            this.updateDropdownSymbol(model.get(this.name));
            this.updateDropdownText(this.currentDropdownSymbol);
            return;
        }

        if (this.currentCurrency && this.currentCurrency.id !== currencyId) {
            this.fetchCurrency();
            this.loadEnumOptions();

            if (app.utils.isTruthy(model.get(this.name)) === false) {
                this.updateDropdownSymbol(model.get(this.name));
                this.updateDropdownText(this.currentDropdownSymbol);
            }
        }
    },

    /**
     * @inheritdoc
     * @override
     */
    _render: function() {
        this._super('_render');

        // This is needed for consistently updating the dropdown symbol if the same option is
        // selected.
        var $el = this.$(this.fieldTag);
        $el.on('select2-close', _.bind(function() {
            this.updateDropdownText(this.currentDropdownSymbol);
        }, this));

        // We need this redundant update when we transition into record view from subpanel for
        // example and on initialize the model doesn't have all the data. Needed because of
        // the differences in rendering record vs subpanel views.
        if (this.model.has('currency_id')) {
            this.fetchCurrency();
            this.loadEnumOptions();
            this.updateDropdownSymbol(this.model.get(this.name));
            this.updateDropdownText(this.currentDropdownSymbol);
        }

        // On the first time rendering, update the dropdown symbol.
        this.updateDropdownText(this.currentDropdownSymbol);
    },

    /**
     * Load the options for this field and pass them to callback function.
     * @override
     */
    loadEnumOptions: function() {
        var currentCurrencyString = this.buildCurrencyString();
        var percentDiscountString = app.lang.get('LBL_DISCOUNT_PERCENT');

        var items = {
            'false': currentCurrencyString,
            'true': percentDiscountString,
        };

        this.items = items;
    },

    /**
     * Resets position of status dropdown if Select2 is active and open
     * and the position of the Select2 container is shifted, which happens
     * when other fields in the headerpane are hidden on status edit
     */
    repositionDropdown: function() {
        if (!this.$el) {
            return;
        }
        var $el = this.$(this.fieldTag).select2('container');

        if ($el.hasClass('select2-dropdown-open')) {
            this.$(this.fieldTag).data('select2').dropdown.css({'left': $el.offset().left});
        }
    },

    /**
     * @inheritdoc
     *
     * This field does not render content in list mode and as an enum when
     * in edit mode.
     *
     * @override
     */
    _loadTemplate: function() {
        this._super('_loadTemplate');

        if (this.action === 'edit') {
            this.template = app.template.getField(
                'enum',
                'edit',
                this.module
            );
        }
    },

    /**
     * Fetches the required currency to be used in the dropdown.
     */
    fetchCurrency: function() {
        if (this.model.has('currency_id')) {
            this.currentCurrency = app.metadata.getCurrency(this.model.get('currency_id'));
        }
    },

    /**
     * Updates the labels for the buttons
     */
    buildCurrencyString: function() {
        var currentCurrencyLabel;

        if (this.currentCurrency) {
            if (app.lang.direction !== 'ltr') {
                currentCurrencyLabel = this.currentCurrency.name + ' ' + this.currentCurrency.symbol;
            } else {
                currentCurrencyLabel = this.currentCurrency.symbol + ' ' + this.currentCurrency.name;
            }
        }

        return currentCurrencyLabel;
    },

    /**
      * Updates the dropdown button icon.
      */
    updateDropdownSymbol: function(value) {
        if (app.utils.isTruthy(value)) {
            this.currentDropdownSymbol = '%';
        } else {
            this.currentDropdownSymbol = this.currentCurrency.symbol;
        }
    },

    /**
     * Helper function for set the text of the dropdown button.
     * Note: We can't use the el.select2('val', value) because that will actually update the value
     * in our dropdown, which was want true/false.
     */
    updateDropdownText: function(value) {
        var elementQueryString = '.' + this.containerCssClass + ' > .select2-choice > .select2-chosen';
        var $dropdownButton = this.$(elementQueryString);
        $dropdownButton.text(value);
    },

    /**
     * @inheritDoc
     * @private
     */
    _dispose: function() {
        this._super('_dispose');
    },
})
