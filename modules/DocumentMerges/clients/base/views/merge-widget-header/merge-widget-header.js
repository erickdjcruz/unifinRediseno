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
 * @class View.Views.Base.DocumentMerges.MergeWidgetHeaderView
 * @alias SUGAR.App.view.views.BaseDocumentMergesMergeWidgetHeaderView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click .template-builder': 'openTemplateBuilder'
    },

    /**
     * Opens the Template Builder help view
     *
     * @param {Event} evt
     */
    openTemplateBuilder: function(evt) {
        window.open(
            '#DocumentMerges/layout/tag-builder',
            'TemplateBuilder',
            'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=415,height=800'
        );
    },
})
