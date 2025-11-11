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
 * @class View.Views.Base.SidebarNavItemQuickcreateView
 * @alias SUGAR.App.view.views.BaseSidebarNavItemQuickcreateView
 * @extends View.Views.Base.SidebarNavItemView
 */

({
    extendsFrom: 'SidebarNavItemView',

    /**
     * @param {Object} options
     * @inheritdoc
     */
    initialize: function(options) {
        options.meta = options.meta || {};
        options.meta.template = options.meta.template || 'sidebar-nav-item';
        this._super('initialize', [options]);

        // shortcut keys
        app.shortcuts.registerGlobal({
            id: 'Quickcreate:Toggle',
            keys: 'c',
            component: this,
            description: 'LBL_SHORTCUT_QUICK_CREATE',
            handler: this.secondaryActionOnClick,
        });
    },

    /**
     * @inheritdoc
     */
    primaryActionOnClick: function() {
        this.secondaryActionOnClick();
    },

    /**
     * @inheritdoc
     */
    secondaryActionOnClick: function() {
        let modules = app.metadata.getModuleNames({
            filter: ['visible', 'quick_create'],
            access: 'create',
        });

        if (_.isEmpty(modules)) {
            return;
        }

        this._super('secondaryActionOnClick');
    }
})
