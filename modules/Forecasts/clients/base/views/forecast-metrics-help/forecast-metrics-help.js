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
 * @class View.Views.Base.ForecastsForecastMetricsHelpView
 * @alias SUGAR.App.view.layouts.BaseForecastsForecastMetricsHelpView
 * @extends View.View
 */
({
    className: 'metrics-help-button inline-block absolute',

    events: {
        'click .metrics-help': 'showHelpModal',
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.helpButton = this.$('.metrics-help');
    },

    /**
     * Info/Guide/Help button click event listener.
     */
    showHelpModal: function() {
        if (!app.isSynced) {
            return;
        }

        if (this.helpButton.hasClass('disabled')) {
            return;
        }

        // For bwc modules and the About page, handle the help click differently.
        if (this.layoutName === 'bwc' || this.layoutName === 'about') {
            this.bwcHelpClicked();
            return;
        }

        if (!this._helpLayout || this._helpLayout.disposed) {
            this._createHelpLayout();
        }

        this._helpLayout.toggle();
    },

    /**
     * Creates the help layout.
     *
     * @param {jQuery} button The Help button.
     * @private
     */
    _createHelpLayout: function() {
        this._helpLayout = app.view.createLayout({
            module: app.controller.context.get('module'),
            type: 'metrics-help',
            button: this.helpButton,
        });

        this._helpLayout.initComponents();

        this.listenTo(this._helpLayout, 'show hide', function(view, active) {
            this.helpButton.toggleClass('active', active);
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        $(window).off('resize');
        app.events.off('metric:data:ready');
        this._super('_dispose');
    }
})
