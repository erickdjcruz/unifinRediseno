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
 * @class View.Views.Base.DRI_LinkExistingActivityHeaderPaneView
 * @alias SUGAR.App.view.views.BaseDRI_LinkExistingActivityHeaderPaneView
 * @extends View.Views.Base.HeaderpaneView
 */
({
    extendsFrom: 'HeaderpaneView',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenToOnce(this.layout, 'selection:closedrawer:fire', function() {
            this.$el.off();
            app.drawer.close();
        });

        this.listenTo(this.layout, 'selection:add:fire', function() {
            this.context.trigger('selection-list:add');
        });

        this._super('bindDataChange');
    },

    /**
     * Overriding to show the module name in the title.
     * @override
     * @param {string} title The unformatted title.
     * @return {string} The formatted title.
     */
    _formatTitle: function(title) {
        let moduleName = app.lang.get('LBL_MODULE_NAME', this.module);
        return app.lang.get(title, this.module, {module: moduleName});
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening(this.layout);
        this._super('_dispose');
    },
});
