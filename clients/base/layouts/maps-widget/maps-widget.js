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
 * Maps widget layout
 *
 * @class View.Layouts.Base.MapsWidgetLayout
 * @alias SUGAR.App.view.layouts.BaseMapsWidgetLayout
 * @extends View.Layouts.Base.SubpanelLayout
 */
({
    extendsFrom: 'SubpanelLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        const module = this._getMapsContextModule();
        const mapsModule = `${module}/maps`;

        /**
         * Replace standard subpanel context with a no link supanel context
         */
        options.context = new app.Context({
            module: module,
            collection: app.data.createBeanCollection(mapsModule),
            model: app.data.createBean('Maps')
        });

        options.context.parent = app.controller.context;

        this._super('initialize', [options]);
    },

    /**
     * Get current context module
     *
     * @return {string}
     */
    _getMapsContextModule: function() {
        const module = app.controller.context.get('module');
        const LOCAL_STORAGE_WIDGET_KEY = `maps_widget_data_${module}`;

        const savedData = app.user.lastState.get(LOCAL_STORAGE_WIDGET_KEY);

        if (savedData && _.has(savedData, 'modules')) {
            return savedData.modules;
        }

        return module;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        //add maps supanel to sortable list
        this.$el.attr({
            'data-subpanel-link': 'maps'
        });
    }
});
