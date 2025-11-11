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
         * @class Core.Actions.OpenUrlAction
         * @alias SUGAR.App.Actions.OpenUrlAction
         *
         * @param {Object} def Action Definition
         */
        function OpenUrl(def) {
            this.def = def;
        }

        /**
         * Evaluate url and open in new tab
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        OpenUrl.prototype.run = function(opts, currentExecution) {
            let actionDef = this.def;

            let formula = actionDef.properties.formula;
            let url = actionDef.properties.url;

            if (app.utils.isTruthy(actionDef.properties.calculated)) {
                this.getCalculatedUrl(opts, formula, currentExecution);
            } else {
                this.openWindowAndContinue(url, currentExecution);
            }
        };

        /**
         * Returns the evaluated value of the url
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {string} formula Sugar Logic Formula
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        OpenUrl.prototype.getCalculatedUrl = function(opts, formula, currentExecution) {
            let recordModel = opts.recordModel;

            const reqType = 'create';
            const url = app.api.buildURL('actionButton', 'evaluateCalculatedUrl');
            const reqMeta = {
                keepTempFieldForCalculatedURL: {
                    buildUrlTempField: {
                        targetField: 'buildUrlTempField',
                        formula: formula
                    }
                },
                recordType: recordModel.module,
                recordId: recordModel.get('id')
            };

            let apiCallbacks = {
                success: function success(resp) {
                    const calculatedUrl = resp.buildUrlTempField.value;
                    this.openWindowAndContinue(calculatedUrl, currentExecution);
                }.bind(this)
            };

            app.api.call(reqType, url, reqMeta, apiCallbacks);
        };

        /**
         * Open new window and continue action execution
         *
         * @param {string} url Url to be opened in a new tab
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        OpenUrl.prototype.openWindowAndContinue = function(url, currentExecution) {
            this.openNewWindow(url);
            currentExecution.nextAction();
        };

        /**
         * Open new browser window with given url
         *
         * @param {string} url Url to be opened in a new tab
         *
         * @return {undefined}
         */
        OpenUrl.prototype.openNewWindow = function(url) {
            const protocolRegex = /^([a-zA-Z]*):.*/gm;

            url = (url || '').trim();
            if (!url.match(protocolRegex)) {
                url = 'https://' + url;
            }

            var newWindow = window.open(url, '_blank');
            newWindow.focus();
        };

        app.actions = app.actions || {};

        app.actions = _.extend(app.actions, {
            OpenUrl: OpenUrl
        });
    });
})(SUGAR.App);
