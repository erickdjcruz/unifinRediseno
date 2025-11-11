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
 * @class View.Fields.Base.DocumentRevisions.NameField
 * @alias SUGAR.App.view.fields.BaseDocumentRevisionsNameField
 * @extends View.Fields.Base.NameField
 */
({
    extendsFrom: 'NameField',

    /**
     * @inheritdoc
     */
    _render: function() {
        if (this.name === 'document_name' && this.view.action === 'create' && this.context.parent &&
            this.context.parent.get('model')) {
            this.model.set('document_name', this.context.parent.get('model').get('document_name'));
        }
        this._super('_render');
    },
})
