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
 * @class View.Views.Base.Reports.ReportWidgetFooterView
 * @alias SUGAR.App.view.views.BaseReportsReportWidgetFooterView
 * @extends View.Views.Base.View
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * @inheritdoc
     */
    _initProperties: function() {
        this.showFooter = true;

        const visibilityForDashlet = this._getFooterVisbilityForDashlet();

        if (!_.isUndefined(visibilityForDashlet)) {
            this.showFooter = visibilityForDashlet;
        }
    },

    /**
     * For dashlet the footer can be hidden, we have to figure out
     * @return {Mixed} - if we are not on dashlet we will get undefined either true/false
     */
    _getFooterVisbilityForDashlet: function() {
        if (!this.layout) {
            return;
        }

        const summationDetailsComponent = this.layout.getComponent('summation-details');
        const summationComponent = this.layout.getComponent('summation');

        //this footer is used in both simple summation also summation with details
        //always there will be only one component available
        if (!summationDetailsComponent && !summationComponent) {
            return;
        }

        let component = summationDetailsComponent ? summationDetailsComponent : summationComponent;

        const summationDetailsLayout = component.layout;

        if (!summationDetailsLayout || !summationDetailsLayout.model) {
            return;
        }

        const dashletListOptions = summationDetailsLayout.model.get('list');

        if (!dashletListOptions) {
            return;
        }

        if (!_.has(dashletListOptions, 'showCount')) {
            return;
        }

        return dashletListOptions.showCount;
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        if (_.has(this, 'layout') && _.has(this.layout, 'layout')) {
            this.listenTo(this.layout.layout, 'panel:collapse', this.toggleFooter, this);
            this.listenTo(this.layout.layout, 'panel:minimize', this.toggleFooter, this);
            this.listenTo(this.context, 'report:set-footer-visibility', this.toggleFooter, this);
        }
    },

    /**
     * Hide/Show footer bar
     *
     * @param {boolean} collapsed
     */
    toggleFooter: function(collapsed) {
        this.$el.toggleClass('hide', collapsed);
    },
})
