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
 * @class View.Views.Portal.ContentsearchFooterView
 * @alias SUGAR.App.view.views.PortalContentsearchFooterView
 * @extends View.View
 */
({
    events: {
        'click [data-action="createcase"]': 'createCase'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.module = 'Cases';
        this.canCreateCase = app.acl.hasAccess('create', this.module);
        this.moduleNames = {module_name: app.lang.getModuleName(this.module)};
        this.hide();
        this.context.on('data:fetched', this.show, this);
        this.context.on('data:fetching', this.hide, this);
    },

    /**
     * Hides the footer.
     */
    hide: function() {
        this.$el.hide();
    },

    /**
     * Shows the footer.
     *
     * @param {Object} data The data to show
     */
    show: function(data) {
        this.data = data;
        this.$el.show();
    },

    /**
     * Shows creation drawer to create a new case.
     */
    createCase: function() {
        var name = this.data && this.data.options.q || '';
        var prefill = app.data.createBean(this.module);
        prefill.set('name', name);

        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                module: this.module,
                model: prefill
            }
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('data:fetched', null, this);
            this.context.off('data:fetching', null, this);
        }
    }
})
