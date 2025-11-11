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
 * @class View.Views.Base.SugarLive.ConfigSidePaneView
 * @alias SUGAR.App.view.views.BaseSugarLiveConfigSidePanelView
 * @extends View.Fields.Base.BaseView
 */
({
    extendsFrom: 'BaseConfigPanelView',

    fieldModels: [],

    events: {
        'click .restore-defaults-btn': 'restoreClicked'
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this.setFieldModels();
        this._super('render');
        // Based on the active tab, show the corresponding config-side-pane div.
        var index = this.context.get('activeTabIndex');
        var sidePanes = this.$('.config-side-pane-all .config-side-pane');
        $(sidePanes[index]).css('display', 'flex');
    },

    /**
     * Create a list of models based on the available modules for summary panel.
     * The modules are needed for rendering the available and selected list fields.
     */
    setFieldModels: function() {
        var availableModules = this.getAvailableModules();
        this.fieldModels = [];
        _.each(availableModules, function(module) {
            var fieldModel = app.data.createBean('SugarLive');
            fieldModel.fieldModule = module;
            this.fieldModels.push(fieldModel);
        }, this);
    },

    /**
     * Returns the list of modules the user has access to
     * and are supported.
     *
     * @return {Array} The list of module names.
     */
    getAvailableModules: function() {
        var selectedModules = this.context.get('enabledModules');
        return _.filter(selectedModules, function(module) {
            return !_.isEmpty(app.metadata.getModule(module));
        });
    },

    /**
     * Trigger event for restoring default values.
     */
    restoreClicked: function(e) {
        var module = e.target.dataset.module;
        this.context.trigger('sugarlive:resetpreview:' + module);
    }
})
