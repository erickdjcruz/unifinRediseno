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
 * @class View.Views.Portal.ModuleMenuView
 * @alias SUGAR.App.view.views.PortalModuleMenuView
 * @extends View.Views.Base.ModuleMenuView
 */
({
    /**
     * Methods called when a `show.bs.dropdown` event occurs. Overrides the base
     * functions to remove population of the favorites and recently viewed records,
     * even if the _defaultSettings metadata is changed
     */
    populateMenu: function() {},
    populate: function(tplName, filter, limit) {},
    getCollection: function(tplName) {}
});
