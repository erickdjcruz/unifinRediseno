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
 * @class View.Fields.Portal.Emails.Htmleditable_tinymceField
 * @alias SUGAR.App.view.fields.PortalEmailsHtmleditable_tinymceField
 * @extends View.Fields.Base.Emails.Htmleditable_tinymceField
 */
({
    extendsFrom: 'BaseEmailsHtmleditable_tinymceField',

    /**
     * @inheritdoc
     *
     * Resize the field's container based on the height of the iframe content
     * for preview.
     */
    setViewContent: function(value) {
        this._super('setViewContent', [value, '../styleguide/assets/css/iframe-sugar.css']);
    }
})
