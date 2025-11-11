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
 * @class View.Layouts.Base.FocusDrawerLayout
 * @alias SUGAR.App.view.layouts.BaseFocusDrawerLayout
 * @extends View.Layouts.Base.SideDrawerLayout
 * @deprecated FocusDrawerLayout controller is deprecated as of 11.2.0. Use SideDrawerLayout instead.
 */
({
    extendsFrom: 'SideDrawerLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        app.logger.warn('FocusDrawerLayout controller is deprecated as of 11.2.0. Use SideDrawerLayout instead.');
        this._super('initialize', [options]);
    }
})
