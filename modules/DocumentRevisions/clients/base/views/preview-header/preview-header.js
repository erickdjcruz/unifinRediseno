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
 * @class View.Views.Base.DocumentRevisions.PreviewHeaderView
 * @alias SUGAR.App.view.views.BaseDocumentRevisionsPreviewHeaderView
 * @extends View.Views.Base.HeaderView
 */
({
    extendsFrom: 'HeaderView',

    /**
     * @inheritdoc
     *
     * @override Overriding to hide preview because we dont allow editing and dont have the correct fields to either
     * turn on or off
     * @private
     */
    _renderFields: function() {
        // this forces the check in the parent to not enter the control structure and not attempt to render the buttons
        this.layout.previewEdit = false;
        this._super('_renderFields');
    },
})
