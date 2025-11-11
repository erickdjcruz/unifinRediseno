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
 * @class View.Views.Base.Cases.MultiLineListView
 * @alias SUGAR.App.view.views.CasesCreateView
 * @extends View.Views.Base.MultiLineListView
 */
({
    /**
     * @inheritdoc
     */
    extendsFrom: 'MultiLineListView',

    /**
     * @inheritdoc
     */
    _setConfig: function(options) {
        this._super('_setConfig', [options]);
        if (this.metric &&
            ['follow_up_datetime'].includes(this.metric.order_by_primary, this.metric.order_by_secondary)) {
            options.meta.collectionOptions.params.order_by += ',case_number:asc';
        }
    }
})
