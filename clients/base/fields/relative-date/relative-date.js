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
 * Display relative time for 'date' field.
 *
 * @class View.Fields.Base.RelativeDateField
 * @alias SUGAR.App.view.fields.BaseRelativeDateField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     *
     * Set a date to end of the day to display relative time correctly, especially when today is the date.
     * Eg, 2019-11-15 will be converted to 2019-11-15T23:59:59. This should be used for 'date' only fields.
     */
    format: function(value) {
        if (value) {
            value = app.date(value).endOf('day').format();
        }
        return value;
    }
})
