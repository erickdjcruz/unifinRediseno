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
 * The base layout for the tag builder component.
 *
 * @class View.Layouts.Base.DocumentMerges.TagBuilderLayout
 * @alias SUGAR.App.view.layouts.BaseDocumentMergesTagBuilderLayout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this.hideApplicationHeaderAndSidebar();

        this.listenTo(this.context, 'change:currentModule', this.hideOptions, this);
        this.listenTo(this.context, 'tag-builder-options:show', this.showOptions, this);
        this.listenTo(this.context, 'tag-builder-options:hide', this.hideOptions, this);
    },

    /**
     * Hide application header and sidebar nav
     */
    hideApplicationHeaderAndSidebar: function() {
        $('.navbar').remove();
        $('#sidebar-nav').remove();
        $('#content').css({
            'top': '0px',
            'left': '0px',
            'height': '100%',
            'overflow-x': 'hidden'
        });
    },

    /**
     * Whenever the module changes, hide the options
     *
     * @param {app.Context} context
     * @param {string} module
     */
    hideOptions: function(context, module) {
        let tabs = this.getComponent('tag-builder-tabs');
        tabs.getComponent('tag-builder-options').hide();
        tabs.getComponent('tag-builder-relationships').show();
        tabs.getComponent('tag-builder-fields').show();
    },

    /**
     * Show field options
     *
     * @param {app.Context} context
     * @param {string} module
     */
    showOptions: function(context, module) {
        let tabs = this.getComponent('tag-builder-tabs');
        tabs.getComponent('tag-builder-fields').hide();
        tabs.getComponent('tag-builder-relationships').hide();
        tabs.getComponent('tag-builder-options').show();
    }
});
