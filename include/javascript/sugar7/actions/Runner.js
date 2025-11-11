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
         * Action Runner
         *
         * @class Core.Actions.Runner
         * @alias SUGAR.App.Actions.Runner
         */
        let Runner = function(actions, ops) {
            this.actions = actions;
            this.ops = ops || {};
            this.queue = [];
        };

        /**
         * Creates a new Runner instance
         *
         * @param {Array} actions
         * @param {Object} ops
         *
         * @return {Core.Actions.Runner}
         */
        Runner.create = function(actions, ops) {
            return new Runner(actions, ops);
        };

        /**
         * Consumes/executes queued actions
         *
         */
        Runner.prototype.consume = function() {
            if (this.queue.length === 0) {
                return;
            }

            let ops = this.ops;
            let nextAction = this.queue.shift();

            try {
                nextAction.run(ops, {
                    nextAction: _.bind(function nextExecution() {
                        this.consume();
                    }, this)
                });
            } catch (err) {
                let failedActions = [nextAction.constructor.name];
                let nActions = failedActions.join(', ');

                const message = app.lang.get('LBL_ACTIONBUTTON_TASK_SEQUENCE_FAILED');
                const bTagS = '<b>';
                const bTagE = '</b> ';

                if (ops.stopOnError) {
                    failedActions = failedActions.concat(this.queue.map(function getActionType(action) {
                        return action.constructor.name;
                    }));
                    nActions = failedActions.join(', ');

                    app.alert.show('actionbutton-action-stop', {
                        level: 'warning',
                        messages: message + bTagS + nActions + bTagE,
                        autoClose: true,
                        autoCloseDelay: 10000,
                    });
                } else {
                    app.alert.show('actionbutton-action-continue', {
                        level: 'warning',
                        messages: message + bTagS + nActions + bTagE,
                        autoClose: true,
                        autoCloseDelay: 10000,
                    });
                    this.consume();
                }
            }
        };

        /**
         * Adds actions to the execution queue
         *
         */
        Runner.prototype.execute = function() {
            _.each(this.actions, function pushAction(val, key) {
                this.queue.push(val);
            }, this);

            this.consume();
        };

        app.actions = app.actions || {};

        app.actions = _.extend(app.actions, {
            Runner: Runner
        });
    });
})(SUGAR.App);
