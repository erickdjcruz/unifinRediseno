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
         * Action Runner plugin adds the ability to a Sidecar component
         * to resolve action definitions to actual actions and
         * also allows for queued execution
         */
        app.plugins.register('ActionRunner', ['field', 'view', 'layout'], {
            /**
             * Creates actions based on definitions and executes them
             *
             * @param {Object} actionsDef
             * @param {Object} opts
             *
             * @return {Core.Actions.Runner}
             */
            execute(actionsDef, opts) {
                let actions = this.resolveDefsToActions(actionsDef);
                let runner = app.actions.Runner.create(actions, opts);

                runner.execute();

                return runner;
            },

            /**
             * Create single action based on given definition
             *
             * @param {Object} actionDef
             *
             * @return {Core.Actions.Runner}
             */
            resolveDefToAction(actionDef) {
                let actionFactory = new app.actions.ActionFactory();
                let action = actionFactory.create(actionDef);
                return action;
            },

            /**
             * Create many actions based on given definitions
             *
             * @param {Object} actionDef
             *
             * @return {Core.Actions.Runner[]}
             */
            resolveDefsToActions(actionDefs) {
                let actions = _.chain(actionDefs)
                    .map(function each(action) {
                        return this.resolveDefToAction(action);
                    }, this)
                    .sortBy(function sortBy(item) {
                        return item.def.orderNumber;
                    }).value();

                return actions;
            },
        });
    });
})(SUGAR.App);
