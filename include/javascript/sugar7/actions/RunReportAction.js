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
(function register(app) {
    app.events.on('app:init', function init() {
        /**
         * Open URL Action
         *
         * @class Core.Actions.RunReportAction
         * @alias SUGAR.App.Actions.RunReportAction
         *
         * @param {Object} def Action Definition
         */
        function RunReport(def) {
            this.def = def;
        }

        /**
         * Open report in a new browser window
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        RunReport.prototype.run = function(opts, currentExecution) {
            const def = this.def;
            const properties = def.properties;
            const reportId = properties.id;

            const url = this.createUrl(reportId);

            this.open(url);

            currentExecution.nextAction();
        };

        /**
         * Open a new browser tab, given url
         *
         * @param {string} url Report url to open in new tab
         */
        RunReport.prototype.open = function(url) {
            window.open(url, '_blank');
        };

        /**
         * Build report detailview url based on report id
         *
         * @param {string} reportId Report id
         *
         * @return {string}
         */
        RunReport.prototype.createUrl = function(reportId) {
            const url = window.location.origin +
                window.location.pathname +
                '#Reports/' +
                reportId;

            return url;
        };

        app.actions = app.actions || {};

        app.actions = _.extend(app.actions, {
            RunReport: RunReport
        });
    });

})(SUGAR.App);
