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
 * @class View.Fields.Base.Documents.NameField
 * @alias SUGAR.App.view.fields.BaseDocumentsNameField
 * @extends View.Fields.Base.NameField
 */
({
    extendsFrom: 'NameField',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        if (!this.model) {
            return;
        }

        // Fill in the document name if its blank the user selects a file
        this.model.on('change:filename', function() {
            if (!this.model.get('document_name')) {
                this.model.set('document_name', this.model.get('filename'));
                this.render();
            }
        }, this);
    }
})
