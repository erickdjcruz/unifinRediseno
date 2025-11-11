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
 * @class View.Fields.Base.Quotes.TextareaField
 * @alias SUGAR.App.view.fields.BaseQuotesTextareaField
 * @extends View.Fields.Base.TextareaField
 */
({
    extendsFrom: 'BaseTextareaField',

    /**
     * @inheritdoc
     *
     * Format the value to a string.
     * Return an empty string for undefined, null and object types.
     * Convert boolean to 1 or 0.
     * Convert array, int and other types to a string.
     *
     * @param {mixed} value to format
     * @return {string} the formatted value
     */
    format: function(value) {
        if (_.isString(value)) {
            if (this.tplName !== 'edit') {
                let shortComment = value;
                var max = this.tplName === 'quote-data-grand-totals-header' ? 20 : this._settings.max_display_chars;
                value = {
                    long: this.getDescription(value, false),
                    defaultValue: value,
                };

                if (value.long && value.long.string.length > max) {
                    value.short = this.getDescription(shortComment, true);
                }
            }

            return value;
        }

        if (_.isUndefined(value) ||
            _.isNull(value) ||
            (_.isObject(value) && !_.isArray(value))
        ) {
            return '';
        }

        if (_.isBoolean(value)) {
            return value === true ? '1' : '0';
        }

        return value.toString();
    }
})
