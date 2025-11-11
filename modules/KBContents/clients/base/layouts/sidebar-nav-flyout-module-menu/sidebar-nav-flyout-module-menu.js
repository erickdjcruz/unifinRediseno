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
 * @class View.Layouts.Base.KBContents.SidebarNavFlyoutModuleMenuLayout
 * @alias SUGAR.App.view.layouts.BaseKBContentsSidebarNavFlyoutModuleMenuLayout
 * @extends View.Layouts.Base.SidebarNavFlyoutModuleMenuLayout
 */
({
    extendsFrom: 'SidebarNavFlyoutModuleMenuLayout',

    /**
     * Root ID of a shown NestedSet.
     * @property {string}
     */
    categoryRoot: null,

    /**
     * Module which implements NestedSet.
     */
    moduleRoot: 'Categories',

    /**
     * Panel label.
     */
    label: 'LNK_LIST_KBCATEGORIES',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.listenTo(app.events, 'tree:list:fire', this.displayCategoriesList, this);
    },

    /**
     * Handle click on KB view categories.
     */
    displayCategoriesList: function() {
        let route = '#KBContents';
        let currentRoute = `#${app.router.getFragment()}`;
        if (currentRoute !== route) {
            app.router.navigate(route, {trigger: true});
        }

        let config = app.metadata.getModule('KBContents', 'config');
        this.categoryRoot = config.category_root || '';

        let treeOptions = {
            category_root: this.categoryRoot,
            module_root: this.moduleRoot,
            plugins: ['dnd', 'contextmenu'],
            isDrawer: true
        };

        let treeCallbacks = {
            onSelect: function() {
                return;
            },
            onRemove: function(node) {
                if (this.context.parent) {
                    this.context.parent.trigger('kbcontents:category:deleted', node);
                }
            }
        };

        app.drawer.open({
            layout: 'nested-set-list',
            context: {
                module: this.moduleRoot,
                parent: this.context,
                title: app.lang.getModString(this.label, this.module),
                treeoptions: treeOptions,
                treecallbacks: treeCallbacks
            }
        });
    },
})
