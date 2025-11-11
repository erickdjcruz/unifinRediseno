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
 * @class View.Layouts.Base.ActivityCardLayout
 * @alias SUGAR.App.view.layouts.BaseActivityCardLayout
 * @extends View.Layout
 */
({
    // Do not show focus drawer icons on links to records within the activity card itself,
    // only in the actions menu
    disableFocusDrawer: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setTimelineType(options);

        if (options.module == 'Audit') {
            // this is a change card, use parent module to add class so correct module icon will be displayed
            this.$el.addClass('activity-card-' + options.context.get('module').toLowerCase());
        }
        this.$el.addClass('activity-card-' + options.module.toLowerCase());
    },

    /**
     * Sets activity timeline type
     *
     * @param options Initialize options
     */
    setTimelineType: function(options) {
        this.timelineType = options.timelineType || 'activity-timeline-base';
    },

    /**
     * Returns activity timeline type
     */
    getTimelineType: function() {
        return this.timelineType;
    },

    /**
     * Override this method to set activity card menu icon visibility
     */
    setCardMenuVisibilities: function() {
    }
})
