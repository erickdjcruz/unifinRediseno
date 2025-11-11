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
 * @class View.Layouts.Base.Users.SelectionListLayout
 * @alias SUGAR.App.view.layouts.BaseUsersSelectionListLayout
 * @extends View.Layouts.Base.SelectionListLayout
 */
({
    extendsFrom: 'BaseSelectionListLayout',

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        this.setUsersFilters();
        this._super('loadData', [options]);
    },

    /**
     * Sets flags on the collection parameters to filter out certain users
     */
    setUsersFilters: function() {
        let params = this.collection.getOption('params') || {};
        params.filterInactive = true;
        params.filterPortal = true;
        this.collection.setOption('params', params);
    }
})
