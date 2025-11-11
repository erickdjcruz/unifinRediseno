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
 * @class View.Views.Base.EmailTemplates.RecordView
 * @alias SUGAR.App.view.views.BaseEmailTemplatesRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    initialize: function(options) {
        this.plugins = _.union(this.plugins, ['EmailTemplates']);
        this._super('initialize', [options]);
    },

    /**
     * @override
     *
     * Override base record.js for Emails Attachment pills. The user can click
     * either the pill, or the span within the pill. Either of these should
     * not trigger entering edit mode
     *
     * @param element
     * @return {boolean}
     */
    hasClickableAction: function(element) {
        var hasClickableAction = this._super('hasClickableAction', [element]);
        hasClickableAction = hasClickableAction || this.$(element).parent().attr('data-action');
        return hasClickableAction;
    }
})
