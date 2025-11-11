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
 * @class View.Layouts.Base.VisualPipelineConfigDrawerContentLayout
 * @alias SUGAR.App.view.layouts.BaseVisualPipelineConfigDrawerContentLayout
 * @extends View.Layouts.Base.ConfigDrawerContentLayout
 */
({
    extendsFrom: 'BaseConfigDrawerContentLayout',

    events: {
        'change select.module-selection': '_changeModule',
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.$el.addClass('record-panel');
        this._changeModule();
    },

    /**
     * Trigger module selection change
     * @private
     */
    _changeModule: function() {
        let selectedModule = this.$el.find('select.module-selection').val();
        this.context.trigger('pipeline:config:set-active-module', selectedModule);
    },
})
