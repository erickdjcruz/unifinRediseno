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
 * @class View.Fields.Base.Notifications.HtmlField
 * @alias SUGAR.App.view.fields.BaseNotificationsHtmlField
 * @extends View.Fields.Base.HtmlField
 */
({
    extendsFrom: 'BaseHtmlField',

    /**
     * For comment log notifications, we need to show who mentioned them
     *
     * @param {string} value The value to format
     * @return {string}
     * @override
     */
    format: function(value) {
        if (value === 'LBL_YOU_HAVE_BEEN_MENTIONED_BY') {
            value = app.lang.get('LBL_YOU_HAVE_BEEN_MENTIONED_BY', this.module);
            var href = app.router.buildRoute('Employees', this.model.get('created_by'), 'detail', true);
            value += ' <a href="#' + href + '">' + this.model.get('created_by_name') + '</a>';
        } else if (this.name === 'description') {
            value = DOMPurify.sanitize(value, {FORBID_TAGS: ['form']});
        }

        return value;
    }
})
