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
 * @class View.Views.Base.Users.RecordView
 * @alias SUGAR.App.view.views.BaseUsersRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    /**
     * Flag to check if we should navigate to Reassign User Records page
     * {boolean}
     */
    triggerReassignUserRecords: false,

    /**
     * Extend the parent function to add editability checking for IDM
     *
     * @param {Array} options
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['HistoricalSummary']);
        this._super('initialize', [options]);

        _.each(this.meta.panels, function(panel) {
            app.utils.setUsersEditableFields(panel.fields, 'record');
        });

        this._initUserTypeViews();
    },

    /**
     * @inheritdoc
     */
    _afterInit: function() {
        this._super('_afterInit');

        // Get a list of names of all the user preference fields on the view
        let userPreferenceFields = [];
        let viewFields = _.flatten(_.pluck(this.meta.panels, 'fields'));
        _.each(viewFields, function(field) {
            if (field.name) {
                let fieldDef = this.model.fields[field.name];
                if (fieldDef && fieldDef.user_preference) {
                    userPreferenceFields.push(field.name);
                }
            }
        }, this);

        // Make sure all user preference fields are added to the options of
        // fields to fetch
        let contextFields = this.context.get('fields') || [];
        contextFields = contextFields.concat(userPreferenceFields);
        this.context.set('fields', _.uniq(contextFields));
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(this.context, 'button:reset_preferences:click', this.resetPreferencesClicked);
        this.listenTo(this.context, 'button:reset_password:click', this.resetPasswordClicked);
        this._super('bindDataChange');
    },

    /**
     * @override
     */
    getDeleteMessages: function() {
        let messages = this._super('getDeleteMessages');
        messages.confirmation = app.lang.get('LBL_DELETE_USER_CONFIRM', this.module);
        return messages;
    },

    /**
     * @override
     */
    deleteModelSuccessCallback: function() {
        this.context.trigger('record:deleted', this._modelToDelete);
        this._modelToDelete = false;
        let url = app.bwc.buildRoute('Users', this.model.get('id'), 'reassignUserRecords');
        app.router.navigate(url, {trigger: true});
    },

    /**
     * @inheritdoc
     */
    _getNoAccessErrorMessage: function(error) {
        if (error.code === 'license_seats_needed' && _.isString(error.message)) {
            return error.message;
        }
        return this._super('_getNoAccessErrorMessage', [error]);
    },

    /**
     * @inheritdoc
     *
     * Handles IDM alert messaging
     */
    editClicked: function() {
        this._super('editClicked');

        if (app.config.idmModeEnabled) {
            let message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_REGULAR_USER', this.module);

            // Admin users should see a link to the SugarIdentity user edit page
            if (app.user.get('type') === 'admin') {
                let link = decodeURI(this.meta.cloudConsoleEditUserLink);
                let linkTemplate = Handlebars.compile(link);
                let url = linkTemplate({
                    record: encodeURIComponent(app.utils.createUserSrn(this.model.get('id')))
                });

                message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_ADMIN_USER', this.module);
                message = message.replace('%s', url);
            }

            app.alert.show('edit-user-record', {
                level: 'info',
                autoClose: false,
                messages: app.lang.get(message)
            });
        }
    },

    /**
     * @inheritdoc
     */
    _saveModelCompleteCallback: function() {
        this._super('_saveModelCompleteCallback');
        if (this.triggerReassignUserRecords) {
            this.triggerReassignUserRecords = false;
            app.alert.show('reassign_records', {
                level: 'confirmation',
                messages: app.lang.get('LBL_REASS_CONFIRM_REASSIGN', this.module),
                onConfirm: _.bind(function() {
                    let url = app.bwc.buildRoute('Users', this.model.get('id'), 'reassignUserRecords');
                    app.router.navigate(url, {trigger: true});
                }, this),
                onCancel: function() {
                    return;
                },
            }, this);
        }
    },

    /**
     * @inheritdoc
     */
    handleSave: function() {
        let changedAttributes = this.model.changedAttributes();
        if (changedAttributes && changedAttributes.status && changedAttributes.status === 'Inactive') {
            this.triggerReassignUserRecords = true;
        }
        this._super('handleSave');
    },

    /**
     * Reset all preferences for this user
     */
    resetPreferencesClicked: function() {
        app.alert.show('reset_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_RESET_PREFERENCES_WARNING_USER', this.module),
            onConfirm: _.bind(function() {
                let url = app.api.buildURL(this.module, `${this.model.get('id')}/resetPreferences`);
                app.api.call('update', url, null, {
                    success: _.bind(function() {
                        this.context.reloadData();
                        app.alert.show('reset_success', {
                            level: 'success',
                            messages: app.lang.get('LBL_RESET_PREFERENCES_SUCCESS_USER', this.module),
                            autoClose: true,
                        });
                    }, this),
                });
            }, this),
            onCancel: function() {
                return;
            }
        });
    },

    /**
     * Reset password for this user
     */
    resetPasswordClicked: function() {
        app.alert.show('reset_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_RESET_PASSWORD_WARNING_USER', 'Users'),
            onConfirm: _.bind(function() {
                let params = {
                    userId: this.model.get('id')
                };
                let url = app.api.buildURL('password/adminRequest');
                app.api.call('create', url, params, {
                    success: function() {
                        app.alert.show('reset_success', {
                            level: 'success',
                            messages: app.lang.get('LBL_NEW_USER_PASSWORD_RESET', 'Users'),
                            autoClose: true,
                        });
                    },
                    error: function(err) {
                        app.logger.error('Failed to trigger a password reset for a user : ' +
                            JSON.stringify(err));
                        app.alert.show('reset_error', {
                            level: 'error',
                            title: app.lang.get('LBL_ERROR'),
                            messages: err.message || app.lang.get('EXCEPTION_UNKNOWN_EXCEPTION')
                        });
                    }
                });
            }, this),
            onCancel: function() {
                return;
            }
        });
    },

    /**
     * Sets up functionality to support special views based on the User type
     *
     * @private
     */
    _initUserTypeViews() {
        // Always fetch is_group and portal_only so we can determine if we need
        // to show their special views
        let contextFields = this.context.get('fields') || [];
        contextFields.push('is_group', 'portal_only');
        this.context.set('fields', _.uniq(contextFields));

        this._checkUserType();
        this.listenTo(this.model, 'change:is_group change:portal_only', this._checkUserType);
        this.listenTo(this.model, 'sync', this._checkAbilityPasswordReset);
    },

    /**
     * Fetches new metadata and re-renders to show special views based on the
     * User type if necessary
     *
     * @private
     */
    _checkUserType: function() {
        let viewType = this.model.get('is_group') ? 'group' :
            this.model.get('portal_only') ? 'portalapi' :
                false;

        if (['group', 'portalapi'].includes(viewType)) {
            this.meta = _.extend({}, app.metadata.getView(null, 'record'),
                app.metadata.getView(this.module, `record-${viewType}`));
            this.render();
            this.handleActiveTab();
        }
    },

    /**
     * Checks the ability of user to trigger a password reset and lefts this item in menu
     * if it is possible
     *
     * @private
     */
    _checkAbilityPasswordReset: function() {
        _.each(this.meta.buttons, function(buttonMeta) {
            if (buttonMeta.name === 'main_dropdown' && !this._accessToResetPassword()) {
                buttonMeta.buttons = _.filter(buttonMeta.buttons, function(button) {
                    return button.name !== 'reset_password';
                });
                this.render();
            }
        }, this);
    },

    /**
     * Checks if user can reset password
     *
     * @return {boolean} true if user can reset password
     * @private
     */
    _accessToResetPassword: function() {
        if (_.isUndefined(this.model.get('user_name'))) {
            return true;
        }

        return app.acl.hasAccess('admin', 'Users') &&
            app.user.get('user_name') !== this.model.get('user_name') &&
            this.model.get('status') === 'Active' &&
            !['SugarCustomerSupportPortalUser', 'SNIPuser'].includes(this.model.get('user_name'));
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    }
})
