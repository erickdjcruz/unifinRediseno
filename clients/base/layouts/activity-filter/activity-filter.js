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
 * @class View.Layouts.Base.ActivityFilterlLayout
 * @alias SUGAR.App.view.layouts.BaseActivityFilterLayout
 * @extends View.Layouts.Base.FilterLayout
 */
({
    extendsFrom: 'FilterLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.context.set('filterList', this.getFilterList());

        this.on('filter:change:module', function(moduleName, linkName, silent) {
            this.context.trigger('filter:change:module', linkName, silent);
        }, this);
    },

    /**
     * Get list of filter menu points
     *
     * @return {Array}
     */
    getFilterList: function() {
        var moduleSingular = app.lang.get('LBL_MODULE_NAME_SINGULAR', this.module) || this.module;
        var filters = [
            {id: 'all_modules', text: app.lang.get('LBL_LINK_ALL')}
        ];

        const moduleMeta = app.metadata.getModule(this.module);
        if (moduleMeta && moduleMeta.isAudited) {
            filters.push({id: 'Audit', text: moduleSingular + ' ' + app.lang.get('LBL_UPDATES')});
        }

        const enabledModules = this.context.get('enabledModules') || [];
        enabledModules.map((module) => {
            if (module === 'Audit' || !app.metadata.getModule(module)) {
                return;
            }

            filters.push({
                id: module,
                text: app.lang.get('LBL_MODULE_NAME', module),
            });
        });

        return filters;
    },

    /**
     * @override
     * @private
     */
    _render: function() {
        this._super('_render');

        var filterId = this.getLastFilter(this.module, this.layoutType);
        var linkName = app.user.lastState.get(app.user.lastState.key(this.name, this)) || 'all_modules';

        this.initializeFilterState(this.name, linkName, filterId);
    },
})
