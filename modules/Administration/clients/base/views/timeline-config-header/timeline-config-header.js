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
 * @class View.Views.Base.AdministrationTimelineConfigHeaderView
 * @alias SUGAR.App.view.views.BaseTimelineConfigHeaderView
 * @extends View.Views.Base.AdministrationConfigHeaderView
 */
({
    extendsFrom: 'AdministrationConfigHeaderView',

    /**
     * Get title for this header
     * @return {string}
     */
    getTitle: function() {
        let title = '';
        let module = this.context.get('target');
        if (module) {
            title = app.lang.get('TPL_ACTIVITY_TIMELINE_SETTINGS', 'Administration',
                {moduleSingular: app.lang.getModuleName(module)});
        }
        return title;
    },

    /**
     * Enable the save button.
     *
     * @inheritdoc
     */
    _render: function(options) {
        this._super('_render', [options]);
        this.enableButton(true);
    },
})
