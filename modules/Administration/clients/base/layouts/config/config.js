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
 * Base layout for Config Framework.
 *
 * @class View.Layouts.Base.AdministrationConfigLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationConfigLayout
 * @extends View.Layouts.Base.Layout
 */
({
    /**
     * Append config view and header based on category
     * @inheritdoc
     */
    _addComponentsFromDef: function(components) {
        var category = this.context.get('category');

        if (category && components) {
            var viewName = category + '-config';
            var viewController = {
                extendsFrom: 'AdministrationConfigView'
            };
            app.view.declareComponent('view', viewName, 'Administration', viewController, false, 'base');
            var headerName = category + '-config-header';
            var headerController = {
                extendsFrom: 'AdministrationConfigHeaderView'
            };
            app.view.declareComponent('view', headerName, 'Administration', headerController, false, 'base');
            var layout = components[0].layout.components[0].layout.components;
            layout.push({
                view: viewName
            }, {
                view: headerName
            });
        }
        this._super('_addComponentsFromDef', [components, this.context, this.context.get('module')]);
    }
})
