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
 * @class View.Fields.Base.Calls.TextareaField
 * @alias SUGAR.App.view.fields.BaseCallsTextareaField
 * @extends View.Fields.Base.TextareaField
 */
({
    extendsFrom: 'textarea',

    transcriptFields: [
        'transcript',
    ],

    /**
     * Add empty-transcript class to transcript fields if the field is empty.
     *
     * @param {string} value - field contents
     * @return {string} value - formatted field contents
     */
    format: function(value) {
        if (_.contains(this.transcriptFields, this.name)) {
            this.$el.toggleClass('empty-transcript', !value);
        }
        return this._super('format', [value]);
    }
})
