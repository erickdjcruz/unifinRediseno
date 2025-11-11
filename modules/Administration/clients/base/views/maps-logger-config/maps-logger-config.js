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
 * @class View.Views.Base.AdministrationMapsLoggerConfigView
 * @alias SUGAR.App.view.views.BaseAdministrationMapsLoggerConfigView
 * @extends View.Views.Base.AdministrationMapsConfigView
 */
({
    extendsFrom: 'AdministrationMapsConfigView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.meta.label = app.lang.get('LBL_MAP_CONFIG_GEOCODE_LOG_VIEWER');
    },
});
