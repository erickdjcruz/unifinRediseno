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
 * @class View.Fields.Base.CjWidgetConfigToggleField
 * @alias SUGAR.App.view.fields.BaseCjWidgetConfigToggleField
 * @extends View.Fields.Base.BaseField
 */
 ({
    /**
     * This field is used in Widget Configuration
     * layout for toggle widget.
     */
    extendsFrom: 'BoolField',
    stateValueMapping: {},

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // Defined in the Meta
        if (this.def) {
            this.stateValueMapping = this.def.stateValueMapping;
            this.keyName = this.def.keyName;
            this.defaultStateValue = this.def.defaultStateValue;
        }

        // The lastState key for local storage.
        this.stateKey = app.user.lastState.buildKey(this.keyName, this.name, this.getCurrentModule());
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.selectedValue = this.getCurrentValue();
        this.model.set(this.name, this.selectedValue);
        this._super('_render');
    },

    /**
     * return the current value of
     * toggle field
     * @return {bool}
     */
    getCurrentValue: function() {
        let selectedMode = this.getToggleFieldState();
        if (_.isEmpty(selectedMode)) {
            selectedMode = this.defaultStateValue;
        }
        return this.stateValueMapping[selectedMode];
    },

    /**
     * Return the default field state from the meta
     * of workflow header
     * @return {string}
     */
    getDefaultFieldState: function() {
        if (this.name && this.view && this.view.name && this.view.module) {
            let deafultLastState = app.user.lastState.buildKey(this.name, this.view.name, this.view.module);
            return app.user.lastState.defaults(deafultLastState);
        }
        return '';
    },

    /**
     * return the field state from cache
     * @return {string}
     */
    getToggleFieldState: function() {
        return app.user.lastState.get(this.stateKey) || this.getDefaultFieldState();
    },

    /**
     * set the field state in cache
     * @return {string}
     */
    setToggleFieldStateInCache: function() {
        let flipedArray = _.invert(this.stateValueMapping);
        let modelValue = !!this.model.get(this.name);
        return app.user.lastState.set(this.stateKey, flipedArray[modelValue]);
    },

    /**
     * return the parent module name
     * @return {string}
     */
    getCurrentModule: function() {
        return (this.context.parent && this.context.parent.get('parentModule')) ||
            this.context.get('parentModule') ||
            this.module || this._module;
    },

    /**
     * @inheritdoc
     */
    _getFallbackTemplate: function(viewName) {
        if (_.contains(['list', 'detail'], viewName)) {
            viewName = 'edit';
        }
        return this._super('_getFallbackTemplate', [viewName]);
    },
});
