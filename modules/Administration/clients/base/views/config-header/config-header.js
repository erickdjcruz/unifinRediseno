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
 * Base header view for Config Framework.
 *
 * @class View.Views.Base.AdministrationConfigHeaderView
 * @alias SUGAR.App.view.views.BaseAdministrationConfigHeaderView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    extendsFrom: 'ConfigHeaderButtonsView',

    /**
     * Config title
     * @property {string}
     */
    title: '',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (!options.meta) {
            options.meta = app.metadata.getView(options.context.get('module'), 'config-header');
        }
        this._super('initialize', [options]);
        this.title = this.getTitle();
    },

    /**
     * @inheritdoc
     */
    _loadTemplate: function(options) {
        this.templateName = this.name;
        this.template = app.template.getView(this.templateName, this.module) ||
            app.template.getView('config-header', this.module);
    },

    /**
     * Disable the save button.
     *
     * @inheritdoc
     */
    _render: function(options) {
        this._super('_render', [options]);
        this.enableButton(false);
    },

    /**
     * Get title for this header
     * @return {string}
     */
    getTitle: function() {
        var title = this.meta && this.meta.label || '';
        if (!title) {
            var category = this.context.get('category');
            if (category) {
                var configView = this.layout.getComponent(category + '-config');
                if (configView) {
                    title = configView.meta.label || '';
                }
            }
        }
        return title;
    },

    /**
     * Trigger save process.
     *
     * @inheritdoc
     */
    saveConfig: function() {
        this.context.trigger('save:config');
    },

    /**
     * Toggle the save button enabled/disabled state.
     *
     * @param {boolean} flag True for enabling the button.
     */
    enableButton: function(flag) {
        this.getField('save_button').setDisabled(!flag);
    }
});
