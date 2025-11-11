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
 * @class View.Views.Base.Users.CopyContentLocaleView
 * @alias SUGAR.App.view.layouts.BaseUsersCopyContentLocaleView
 * @extends View.Views.Base.View
 */

({
    /**
     * @inheritdoc
     */
    events: {
        'change .fromUser': 'loadSettings',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        this.currentUserId = app.user.id;
        this.retrieveUsers();
        this.getData();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render', arguments);

        this.renderDropdowns();
    },

    /**
     * Loads the settings for the selected user
     *
     * @param {Event} evt
     */
    loadSettings: function(evt) {
        const userData = this.$(evt.currentTarget).find(':selected').data();

        if (_.isEmpty(userData)) {
            return;
        }

        this.currentUserId = userData.id;
        this.getData();
    },

    /**
     * retrieve the users in order to display them in the user select field
     */
    retrieveUsers: function() {
        const usersUrl = app.api.buildURL('Users', null, null, {
            filter: [{
                status: {
                    $equals: 'Active'
                }
            }],
            max_num: -1,
            order_by: 'first_name:asc',
        });

        app.api.call('read', usersUrl, null, {
            success: _.bind(function(data) {
                this.users = data.records;
                this.render();
            }, this),
            error: _.bind(function(error) {
                app.alert.show('user-utils-error', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_USER_UTILS_DATA_ERROR', this.module),
                });
            }, this),
        });
    },

    /**
     * Creates the select2 dropdowns.
     */
    renderDropdowns: function() {
        this.$('select').select2({
            allowClear: true,
            containerCss: 'select2-choices-pills-close',
        });
    },

    /**
     * Gets the locales data for the current user.
     */
    getData: function() {
        const currentUser = this.currentUserId;
        const localeUrl = app.api.buildURL('userUtilities', `getLocaleData/${currentUser}`);

        app.api.call('read', localeUrl, null, {
            success: _.bind(function(data) {
                this.locales = data;
                this.render();
            }, this),
            error: function(error) {
                app.alert.show('user-utils-error', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_USER_UTILS_DATA_ERROR', this.module),
                });
            }
        });
    },
});
