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
 * @class View.Fields.Base.DocumentRevisions.ActiondropdownField
 * @alias SUGAR.App.view.fields.BaseDocumentRevisionsActiondropdownField
 * @extends View.Fields.Base.ActiondropdownField
 */
({
    extendsFrom: 'ActiondropdownField',

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // This is to ensure the single button renders with correct padding (since there is no dropdown for Doc Revs)
        if (this.module === 'DocumentRevisions') {
            this.$el.toggleClass('btn-group', true);
        }

        return this;
    },
})
