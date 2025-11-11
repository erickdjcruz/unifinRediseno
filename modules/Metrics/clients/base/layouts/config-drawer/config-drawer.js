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
 * @class View.Layouts.Base.MetricsConfigDrawerLayout
 * @alias SUGAR.App.view.layouts.BaseMetricsConfigDrawerLayout
 * @extends View.Layouts.Base.ConfigDrawerLayout
 */
({
    extendsFrom: 'BaseConfigDrawerLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
    },

    /**
     * Sets up the models for each of the enabled modules from the configs
     */
    loadData: function(options) {
        this.collection.add(app.data.createBean(this.module));
    },

    /**
     * @return {boolean}
     * @private
     */
    _checkConfigMetadata: function() {
        return true;
    },

})
