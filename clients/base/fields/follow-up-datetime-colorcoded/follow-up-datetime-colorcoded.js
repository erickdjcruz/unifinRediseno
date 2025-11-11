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
 * @class View.Fields.Base.FollowUpDatetimeColorcodedField
 * @alias SUGAR.App.view.fields.FollowUpDatetimeColorcodedField
 * @extends View.Fields.Base.RelativeTimeField
 */
({
    extendsFrom: 'RelativeTimeField',

    /**
     * List of additional CSS classes to apply when showing a colored pill.
     *
     * @type string[]
     * @private
     */
    _defaultExtraClasses: ['label', 'pill'],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        /**
         * Map of keys of CSS class name(s)
         *
         * @type {Object}
         * @private
         */
        this._colorCodeClasses = this.def.color_code_classes || {};
        this._hideOnEdit = this.def.hide_on_edit || false;

    },

    /**
     * @inheritdoc
     *
     * Set color on render
     * Don't render if action is edit and hideOnEdit is true
     */
    _render: function() {
        if (this.action === 'edit' && this._hideOnEdit) {
            return;
        }

        this._super('_render');
        this.setColorCoding();
    },

    /**
     * Set color coding based on relative time value.
     * This is only applied when the action is list or detail
     */
    setColorCoding: function() {
        this._clearColorCode();

        if (!this.model || (this.action !== 'list' && this.action !== 'detail')) {
            return;
        }

        this._setColorCodeClass(this._getColorCodeLabel());
    },

    /**
     * Get label for the color code class base on follow up datetime value
     *
     * @return {string} label to get color code class
     * @private
     */
    _getColorCodeLabel: function() {
        var value = this.model.get(this.name);

        if (_.isEmpty(value)) {
            return '';
        }

        var followUpDate = app.date(value);
        var now = app.date();

        if (followUpDate.isBefore(now)) {
            return 'overdue';
        } else if (followUpDate.subtract(1, 'days').isBefore(now)) {
            return 'in_a_day';
        } else {
            return 'more_than_a_day';
        }
    },

    /**
     * Set the color code class to the field tag.
     *
     * @param {string} colorCodeClass Color code class name.
     * @private
     */
    _setColorCodeClass: function(colorCodeLabel) {
        if (colorCodeLabel) {
            var defaultClasses = this._defaultExtraClasses.join(' ');
            var colorCodedClass = this._colorCodeClasses[colorCodeLabel];
            if (colorCodedClass) {
                this.$el.addClass(defaultClasses + ' ' + colorCodedClass);
            } else {
                this.$el.addClass(defaultClasses);
            }
        }
    },

    /**
     * Clear color coding classes.
     * @private
     */
    _clearColorCode: function() {
        var classes = _.union(_.values(this._colorCodeClasses), this._defaultExtraClasses).join(' ');
        this.$el.removeClass(classes);
    }
})
