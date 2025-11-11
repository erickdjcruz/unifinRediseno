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
 * @class View.Views.Base.ActivityCardMenuView
 * @alias SUGAR.App.view.views.BaseActivityCardMenuView
 * @extends View.Views.Base.CabmenuView
 */
({
    extendsFrom: 'CabmenuView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.template = app.template.getView('cabmenu');
        this.setActivityModel();
    },

    /**
     * Return card-menu metadata for current module
     *
     * @return {Array}
     */
    getCabMeta: function() {
        const meta = app.metadata.getView(this.module, 'activity-card-definition');

        if (meta && meta.card_menu) {
            return meta.card_menu;
        }

        return [];
    },

    /**
     * Return activity-timeline metadata for current module
     *
     * @param {string} metaViewName name of the view for which metadata should be fetched
     * @return {Array}
     */
    getActivitiesMeta: function(metaViewName) {
        return app.metadata.getView(this.context.get('module'), metaViewName);
    },

    /**
     * Get the name of the timeline view
     */
    getViewNameForMeta: function() {
        return this.getActivityCardLayout().getTimelineType();
    },

    /**
     * Get the activity-card layout
     *
     * @return {Object}
     */
    getActivityCardLayout: function() {
        return this.closestComponent('activity-card');
    },

    /**
     * Set the activity model from the activity-card layout
     */
    setActivityModel: function() {
        var layout = this.getActivityCardLayout();

        if (layout && layout.model) {
            this.model = layout.model;
        }
    },

    /**
     * Get context model for email actions.
     * @return {Bean}
     */
    getContextModel: function() {
        return this.model;
    },

    /**
     * Reload dashlet content
     */
    reloadData: function() {
        if (this.$el) {
            this.$el.closest('.dashlet').find('[data-dashletaction=reloadData]').trigger('click');
        }
    },
})
