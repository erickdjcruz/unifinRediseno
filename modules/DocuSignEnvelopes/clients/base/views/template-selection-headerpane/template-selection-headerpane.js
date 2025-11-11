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
 * @class View.Views.Base.DocuSignEnvelopes.TemplateSelectionHeaderpaneView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesTemplateSelectionHeaderpaneView
 * @extends View.Views.Base.View
 */
 ({
    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this._super('_renderHtml');

        this.layout.off('selection:closedrawer:fire');
        this.layout.once(
            'selection:closedrawer:fire',
            _.once(
                _.bind(function closeDrawer() {
                    this.$el.off();
                    app.drawer.close({
                        closeEvent: true
                    });
                }, this)
            )
        );
    },
});
