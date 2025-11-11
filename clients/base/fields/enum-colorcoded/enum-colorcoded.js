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
 * EnumColorCodedField is an enum field that sets a background color
 * based on its value.
 * - Background colors will map to default colors based on the order of enum options
 *   from native Javascript method `Object.keys`.
 * - For example:
 *     - if `Object.keys` returns ['New', 'Duplicate', 'Pending Input', 'a_custom_option'],
 *       the color will be "dark-green, blue, dark-red, purple" respectively.
 *     - if `Object.keys` returns ['New', 'Duplicate', 'another_custom_option', 'Pending Input', 'a_custom_option']
 *       the color will be "dark-green, blue, dark-red, purple, dark-teal" respectively.
 *
 * @class View.Fields.Base.EnumColorCodedField
 * @alias SUGAR.App.view.fields.BaseEnumColorCodedField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * List of additional CSS classes to apply when showing a colored pill.
     *
     * @type string[]
     * @private
     */
    _defaultExtraClasses: ['label', 'pill', 'text-white'],

    /**
     * List of default color codes
     *
     * @type string[]
     * @private
     */
    _defaultColorCodes: [
        'dark-green',
        'blue',
        'dark-red',
        'purple',
        'dark-teal',
        'dark-blue',
        'dark-coral',
        'dark-orange',
        'bright-blue',
        'red',
        'gray-light',
        'gray-dark'
    ],

    /**
     * List of additional CSS classes to apply based on action.
     *
     * @type string[]
     * @private
     */
    _defaultActionClasses: ['list', 'detail'],

    /**
     * @inheritdoc
     *
     * Listen for a change to this field's value and set color coding
     * appropriately.
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.model.on('change:' + this.name, this.setColorCoding, this);
    },

    /**
     * @inheritdoc
     *
     * Checks color code conditions to determine if this field should have
     * color applied to it.
     */
    _render: function() {
        this.type = 'enum'; // use enum templates
        this._super('_render');
        this.setColorCoding();
    },

    /**
     * Set color coding based on enum value.
     * This is only applied when the action is detail or list (not inline edit on
     * list view).
     */
    setColorCoding: function() {
        this._clearColorCode();

        if (!this.model || (this.action !== 'list' && this.action !== 'detail')) {
            return;
        }

        this._setColorCodeClass(this._getColorCodeClass());
    },

    /**
     * Gets color code class based on default order of enum options.
     *
     * @return {string} One of the color codes or an empty string
     *   if no color code.
     * @private
     */
    _getColorCodeClass: function() {
        var value = this.model.get(this.name);

        if (_.isEmpty(value) || !_.isObject(this.items)) {
            return '';
        }

        var codeIndex = Object.keys(this.items).indexOf(value) % this._defaultColorCodes.length;
        return this._defaultColorCodes[codeIndex] || '';
    },

    /**
     * Set the color code class to the field tag.
     *
     * @param {string} colorCodeClass Color code class name.
     * @private
     */
    _setColorCodeClass: function(colorCodeClass) {
        if (colorCodeClass) {
            this.$el.addClass(this._defaultExtraClasses.join(' ') + ' ' + colorCodeClass + ' ' + this.action);
        }
    },

    /**
     * Clear color coding classes.
     * @private
     */
    _clearColorCode: function() {
        var classes = _.union(this._defaultColorCodes, this._defaultExtraClasses, this._defaultActionClasses).join(' ');
        this.$el.removeClass(classes);
    }
})
