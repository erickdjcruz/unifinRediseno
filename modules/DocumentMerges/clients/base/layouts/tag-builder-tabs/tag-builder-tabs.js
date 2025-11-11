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
 * The layout for the tag builder tabs component.
 *
 * @class View.Layouts.Base.DocumentMerges.TagBuilderTabsLayout
 * @alias SUGAR.App.view.layouts.BaseDocumentMergesTagBuilderTabsLayout
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click [data-toggle=tab]': 'switchTab',
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');
        this._renderTooltips();
    },

    /**
     * Switch Tabs
     *
     * @param {Event} evt
     */
    switchTab: function(evt) {
        evt.preventDefault();

        const dataTarget = evt.target.dataset.target;

        this.$('.tab-content .tab-pane').hide();
        this.$('.tab-content #' + dataTarget).show();
    },

    /**
     * Render tooltips associated to this layout
     */
    _renderTooltips: function() {
        this.$('[rel=tooltip]').tooltip();
    },
});
