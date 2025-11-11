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
 * @class View.Views.Base.ConsoleConfiguration.ConfigSidePaneView
 * @alias SUGAR.App.view.views.BaseConsoleConfigurationConfigSidePanelView
 * @extends View.Fields.Base.BaseView
 */
({
    extendsFrom: 'BaseConfigPanelView',

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        // Based on the active tab, show the corresponding config-side-pane div
        var index = this.context.get('activeTabIndex');
        var sidePanes = this.$('.config-side-pane-all .config-side-pane');
        $(sidePanes[index]).css('display', 'flex');
    }
})
