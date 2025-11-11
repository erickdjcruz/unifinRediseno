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
 * @class View.Views.Base.ActivityFilterModuleDropdownView
 * @alias SUGAR.App.view.views.BaseActivityFilterModuleDropdownView
 * @extends View.Views.Base.FilterModuleDropdownView
 */
({
    extendsFrom: 'FilterModuleDropdownView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options.template = app.template.get('filter-module-dropdown');

        this._super('initialize', [options]);

        this.layout.on('filter:change:module', this.filterChangeHandler, this);
    },

    /**
     * @inheritdoc
     * @return {Object}
     */
    getFilterList: function() {
        return this.context.get('filterList');
    },

    /**
     * @inheritdoc
     * @return boolean
     */
    shouldDisableFilter: function() {
        return false;
    },

    /**
     * Handler for the filter changes
     * @param {string} linkModuleName
     * @param {string} linkName
     */
    filterChangeHandler: function(linkModuleName, linkName) {
        const cacheKey = app.user.lastState.key(this.layout.name, this.layout);

        if (linkName) {
            app.user.lastState.set(cacheKey, linkName);
        } else {
            app.user.lastState.remove(cacheKey);
        }
    },

    /**
     * @inheritdoc
     */
    initSelection: function(el, callback) {
        let selection = {};
        if (_.findWhere(this.filterList, {id: el.val()})) {
            selection = _.findWhere(this.filterList, {id: el.val()});
        } else if (this.filterList && this.filterList.length > 0)  {
            selection = this.filterList[0];
        }
        callback(selection);
    }
})
