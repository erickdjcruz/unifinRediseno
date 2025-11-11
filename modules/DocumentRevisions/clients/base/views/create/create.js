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
 * @class View.Views.Base.DocumentRevisions.CreateView
 * @alias SUGAR.App.view.views.BaseDocumentRevisionsCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * Save and close drawer
     */
    saveAndClose: function() {
        this.initiateSave(_.bind(function() {
            if (this.closestComponent('drawer')) {
                app.drawer.close(this.context, this.model);
                // Triggers an event on the Document record view to update field values to correspond with most recent
                // document revision
                this.context.parent.trigger('documentrevisions:save');
            } else {
                app.navigate(this.context, this.model);
            }
        }, this));
    },
})
