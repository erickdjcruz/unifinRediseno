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
 * @class View.Views.Base.AdministrationSearchbarView
 * @alias SUGAR.App.view.views.BaseAdministrationSearchbarView
 * @extends View.Views.Base.SearchbarView
 */
({
    /**
     * @inheritdoc
     */
    className: 'admin-searchbar',

    /**
     * @inheritdoc
     */
    extendsFrom: 'SearchbarView',

    /**
     * @inheritdoc
     *
     * @private
     */
    _initProperties: function() {
        this.module = 'Administration';
        this.greeting = app.lang.get('LBL_ADMIN_SEARCHBAR_GREETING', this.module);
        if (this.layout) {
            this.layout.on('admin:panel-defs:fetched', function() {
                this.sourceDataReady();
            }, this);
        }
    },

    /**
     * @inheritdoc
     *
     * @private
     */
    _populateLibrary: function() {
        this.library = [];
        var defs = this.layout.getAdminPanelDefs();
        _.each(defs, function(category) {
            _.each(category.options, function(item) {
                var action = {
                    name: app.lang.get(item.label, this.module),
                    description: app.lang.get(item.description, this.module),
                    href: item.link
                };
                this.library.push(action);
            }, this);
        }, this);
    }
})
