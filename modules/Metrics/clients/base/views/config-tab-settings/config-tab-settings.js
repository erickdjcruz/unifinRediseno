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
 * @class View.Views.Base.Metrics.ConfigTabSettingsView
 * @alias SUGAR.App.view.views.BaseMetricsConfigTabSettingsView
 * @extends View.Views.Base.ConsoleConfiguration.ConfigPaneView
 */
({
    extendsFrom: 'BaseConsoleConfigurationConfigPanelView',

    events: {
        'click .restore-defaults-btn': 'handleRestoreDefaults'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.isAdmin = this.checkAdminAccess();
    },

    /**
     * Checks Metrics ACLs to see if the User is a system admin
     * or if the user has a admin role for the Metrics module
     */
    checkAdminAccess: function() {
        let acls = app.user.getAcls().Metrics;
        let isAdmin = !_.has(acls, 'admin');
        let isSysAdmin = (app.user.get('type') === 'admin');

        return (isSysAdmin || isAdmin);
    },

    /**
     * Handles the click event on the restore defaults button
     */
    handleRestoreDefaults: function() {
        app.alert.show('reset_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_RESTORE_DEFAULT_CONFIRM', 'Metrics'),
            onConfirm: _.bind(function() {
                this.restoreAdminDefaults();
            }, this)
        }, this);
    },

    /**
     * Restores the metrics to their initial state, i.e., all the metrics are visible and none are hidden.
     * This also get any new metrics that the admin might have created
     */
    restoreAdminDefaults: function() {
        let metricContext = this.model.get('metric_context');
        let metricModule = this.model.get('metric_module');
        if (!metricContext || !metricModule) {
            return;
        }

        let url = app.api.buildURL('Metrics', 'restore-defaults', null, {
            metric_context: metricContext,
            metric_module: metricModule
        });
        app.api.call('read', url, null, {
            success: _.bind(function(results) {
                // empty the hidden fields column
                this.$('#fields-sortable').empty();

                if (!_.isEmpty(results)) {
                    let visibleFieldComp = this.getField('visible-fields') || {};
                    // empty the previously stored metrics
                    visibleFieldComp.visibleFields = [];
                    _.each(results, function(field) {
                        visibleFieldComp.visibleFields.push({
                            'name': field.id,
                            'displayName': field.name
                        });
                    }, this);
                    visibleFieldComp.renderAfterFetch();
                }
            }, this)
        });
    }
})
