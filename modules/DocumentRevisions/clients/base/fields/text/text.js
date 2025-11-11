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
 * @class View.Fields.Base.DocumentRevisions.TextField
 * @alias SUGAR.App.view.fields.BaseDocumentRevisionsTextField
 * @extends View.Fields.Base.TextField
 */
({
    extendsFrom: 'TextField',

    /**
     * @inheritdoc
     * @override
     */
    _initDefaultValue: function() {
        // Need to grab default values from parent context when creating a Document Revision
        if (this.name === 'latest_revision' && this.view.action === 'create' && this.context.parent &&
            this.context.parent.get('model')) {
            var latestRev = this.context.parent.get('model').get('revision');
            this.model.set('latest_revision', latestRev);
        } else if (this.name === 'revision' && this.view.action === 'create' && this.context.parent &&
            this.context.parent.get('model')) {
            var rev = (parseInt(this.context.parent.get('model').get('revision')) + 1).toString();
            this.model.set('revision', rev);
        }
        this._super('_initDefaultValue');
    },
})
