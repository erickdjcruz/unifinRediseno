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
         * Record assignment action
         *
         * @class Core.Actions.AssignRecordAction
         * @alias SUGAR.App.Actions.AssignRecordAction
         *
         * @param {Object} def Action Definition
         */
        function AssignRecord(def) {
            this.def = def;
        }

        /**
         * After we patch the record in run() we will need to refetch the current context model.
         * This function basically re-applies whatever changes the user might've done to the model
         * and has not synched to the server to the re-fetched model
         *
         * @param {Data.Bean} model Current bean model
         * @param {Object} updateFields A simple object with field values that were updated by action
         *
         */
        AssignRecord.prototype.fetchAndApplyChanges = function(model, updatedFields) {
            let changes = model.changedAttributes(model.getSynced());
            changes = !_.isEmpty(changes) ? _.pick(model.changedAttributes(), _.keys(changes)) : {};
            changes = _.omit(changes, _.keys(updatedFields));
            changes = _.omit(changes, ['date_modified']);

            model.fetch({
                complete: function reapplyChanges() {
                    model.set(changes);
                }
            });
        },

        /**
         * Execute action
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        AssignRecord.prototype.run = function(opts, currentExecution) {
            let actionDef = this.def;

            const assignToCurrentUser = actionDef.properties.assignToCurrentUser;

            let newUserId = assignToCurrentUser ? app.user.get('id') : actionDef.properties.id;
            let useUserName = assignToCurrentUser ? app.user.get('full_name') : actionDef.properties.name;

            let recordModel = opts.recordModel;

            if (!this.hasEditAccess(opts.recordModel)) {
                app.alert.show('alert_no_access', {
                    level: 'error',
                    title: app.lang.get('ERR_NO_VIEW_ACCESS_TITLE'),
                    messages: app.lang.get('ERR_NO_VIEW_ACCESS_ACTION'),
                    autoClose: true,
                    autoCloseDelay: 5000
                });

                currentExecution.nextAction();

                return;
            };

            let updatedFields = {
                assigned_user_id: newUserId || '',
                assigned_user_name: useUserName || ''
            };

            let patchModel = app.data.createBean(recordModel.module, _.assign({
                id: recordModel.get('id'),
            }, updatedFields));

            patchModel.save({}, {
                showAlerts: true,
                success: _.bind(function successSave() {
                    this.fetchAndApplyChanges(recordModel, updatedFields);
                    currentExecution.nextAction();
                }, this),
                error: _.bind(function errorSave() {
                    this.fetchAndApplyChanges(recordModel, updatedFields);
                    currentExecution.nextAction();
                }, this)
            });
        };

        /**
         * Check acl access for assigned user fields
         *
         * @param {Data.Bean} model
         *
         * @return {boolean}
         */
        AssignRecord.prototype.hasEditAccess = function(model) {
            let hasAccess = app.acl.hasAccessToModel('edit', model) &&
                app.acl.hasAccessToModel('edit', model, 'assigned_user_id') &&
                app.acl.hasAccessToModel('edit', model, 'assigned_user_name');

            return hasAccess;
        };

        app.actions = app.actions || {};

        app.actions = _.extend(app.actions, {
            AssignRecord: AssignRecord
        });
    });
})(SUGAR.App);
