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
 * @class View.Layouts.Base.Forecasts.FilterpanelLayout
 * @alias SUGAR.App.view.layouts.BaseForecastsFilterpanelLayout
 * @extends View.Layouts.Base.FilterpanelLayout
 */
({
    extendsFrom: 'FilterpanelLayout',

    /**
     * Add forecasts:refreshlist event when refresh button was clicked
     *
     * @private
     */
    _refreshList: function() {
        this._super('_refreshList');
        this.context.trigger('forecasts:refreshList');
    },
})
