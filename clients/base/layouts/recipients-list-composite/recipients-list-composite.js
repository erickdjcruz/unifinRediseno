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
 * @class View.Layouts.Base.RecipientsListCompositeLayout
 * @alias SUGAR.App.view.layouts.BaseRecipientsListCompositeLayout
 * @extends View.Layouts.Base.SelectionListModuleSwitchLayout
 */
({
    extendsFrom: 'SelectionListModuleSwitchLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);
    },

    /**
     * Before init
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        options.context.set('filterList', ['Users', 'Accounts', 'Contacts', 'Leads']);
    },

    /**
     * Reload this drawer layout.
     *
     * @param {string} module
     */
    reload: function(module) {
        const self = this;
        // Need to defer so that we do not reload and dispose the drawer before all event
        // callbacks have completely finished.
        _.defer(function() {
            app.drawer.load({
                layout: 'recipients-list-composite',
                type: 'recipients-list-composite',
                context: {
                    module: module,
                    fields: self.context.get('fields'),
                    filterOptions: self.context.get('filterOptions'),
                    filterList: self._filterList,
                    mixed_collection: self.context.get('mixed_collection'),
                    templateDetails: self.context.get('templateDetails'),
                    storedRoles: self.context.get('storedRoles')
                }
            });
        });
    },
});
