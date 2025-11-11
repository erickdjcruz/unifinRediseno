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
 * @class View.Views.Reports.ReportDashletSelector
 * @alias SUGAR.App.view.views.BaseReportDashletSelector
 * @extends View.Views.Base.View
 */
({
    events: {
        'click [data-type=button]': 'onClick',
    },
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Init Properties
     */
    _initProperties: function() {
        this._selectedView = this.layout.model.get('defaultSelectView');
        this._shouldManuallyHandleButtons = true;
        this._hasChart = true;

        const lastState = this.layout.model.get('userLastState');

        if (lastState && _.has(lastState, 'defaultView')) {
            this._selectedView = lastState.defaultView;
        }


        if (_.has(this, 'layout') && this.layout && _.has(this.layout, 'layout') && this.layout.layout) {
            const reportDashletComponent = this.layout.layout.getComponent('report-dashlet');

            this._hasChart = reportDashletComponent.settings.get('chartType') !== 'none' &&
                            reportDashletComponent.settings.get('reportType') !== 'tabular';

            if (!this._hasChart && this._selectedView === 'chart') {
                this._activateTab('list');
            }
        }
    },

    /**
     * Select the view type
     *
     * @param {Event} e
     */
    onClick: function(e) {
        const type = e.currentTarget.getAttribute('data-name');

        this._activateTab(type);
    },

    /**
     * Activate chosen tab
     *
     * @param {string} type
     */
    _activateTab: function(type) {
        this._selectedView = type;

        this.context.trigger('report-dashlet:change:view-type', {type});

        //after the upgrade to bootstrap 5.x we have to handle it manually
        this._setSelectedButton();

        this.$('.nav-tabs').toggleClass('hidden', type === 'list');
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this._shouldManuallyHandleButtons) {
            this._setSelectedButton(true);
        }
    },

    /**
     * Since we are caching the selected button
     * first time when the view is loaded if we have something cached
     * we have to manually select the button
     *
     * @param {boolean} manuallyHandle
     */
    _setSelectedButton: function(manuallyHandle = false) {
        const buttons = this.$('[data-type=button]');

        if (buttons && buttons.length < 1) {
            return;
        }

        _.each(buttons, function each(button) {
            button = $(button);

            const buttonName = button.attr('data-name');

            if (buttonName === this._selectedView && !button.hasClass('active')) {
                button.addClass('active');

                return;
            }

            if (buttonName !== this._selectedView && button.hasClass('active')) {
                button.removeClass('active');
            }

            if (manuallyHandle) {
                this._shouldManuallyHandleButtons = false;
            }
        }, this);

        if (manuallyHandle) {
            this.$('.nav-tabs').toggleClass('hidden', this._selectedView === 'list');
        }
    },
});
