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
 * @class View.Fields.Base.ConsoleConfiguration.SortOrderSelectorField
 * @alias SUGAR.App.view.fields.BaseConsoleConfigurationSortOrderSelectorField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click .sort-order-selector': 'setNewValue'
    },

    /**
     * Stores the name of the field that this field is conditionally dependent on
     */
    dependencyField: null,

    /**
     * @inheritdoc
     *
     * Grabs the name of the dependency field from the field options
     *
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        if (options.def && options.def.dependencyField) {
            this.dependencyField = options.def.dependencyField;
        }
    },

    /**
     * @inheritdoc
     *
     * Extends the parent bindDataChange to include a check of the value of
     * the dependency field
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        if (this.dependencyField) {
            this.model.on('change:' + this.dependencyField, function() {
                this._handleDependencyChange();
            }, this);
            this.model.on('change:' + this.name, function() {
                this._setValue(this.model.get(this.name));
            }, this);
        }
    },

    /**
     * When this field first renders, check the dependency field to see if we
     * need to hide this
     *
     * @private
     */
    _render: function() {
        this._super('_render');
        this._handleDependencyChange();
    },

    /**
     * Checks the value of the dependency field. If it is empty, this field will
     * be set to 'ascending' and hidden.
     *
     * @private
     */
    _handleDependencyChange: function() {
        if (this.model && this.$el) {
            if (_.isEmpty(this.model.get(this.dependencyField))) {
                this._setValue('asc');
                this.$el.hide();
            } else {
                this.$el.show();
            }
        }
    },

    /**
     * Simulates the user clicking on the field to set a value for this field
     * (both on the model and in the UI)
     *
     * @param value the value ('asc' or 'desc') to set the field to
     * @private
     */
    _setValue: function(value) {
        this.$el.find('[name="' + value + '"]').click();
    },

    /**
     * Sets the value of the selected sort order on the model
     *
     * @param event the button click event
     */
    setNewValue: function(event) {
        this.model.set(this.name, event.currentTarget.name);
    }
})
