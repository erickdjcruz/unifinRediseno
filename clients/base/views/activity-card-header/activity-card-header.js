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
 * @class View.Views.Base.ActivityCardHeaderView
 * @alias SUGAR.App.view.views.BaseActivityCardHeaderView
 * @extends View.Views.Base.ActivityCardView
 */
({
    extendsFrom: 'ActivityCardView',

    className: 'activity-card-header',

    /**
     * The panel_users panel metadata
     */
    usersPanel: null,

    /**
     * The panel_header panel metadata
     */
    headerPanel: null,

    /**
     * A list of user field definitions
     */
    userList: [],

    /**
     * Flag to store if the app is in RTL or not
     */
    isRtl: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.setUsersPanel();
        this.setHeaderPanel();
        this.addNumber();

        this.isRtl = app.lang.direction === 'rtl';
    },

    /**
     * Add number to name field
     */
    addNumber: function() {
        const numberField = this.moduleSingular.toLowerCase().replace(/\s+/g, '') + '_number';

        if (!this.activity) {
            return;
        }

        const numberValue = this.activity.get(numberField);

        if (!numberValue) {
            return;
        }

        const nameValue = numberValue + ' ' + this.activity.get('name');
        this.activity.set('name', nameValue, {silent: true});
    },

    /**
     * Set relevant metadata from the users panel
     */
    setUsersPanel: function() {
        this.setUsersTemplate();
        this.setUsersFields();
    },

    /**
     * Get and cache the users panel
     *
     * @return {Object}
     */
    getUsersPanel: function() {
        const userPanelsMap = {
            panel_users: 'usersPanel',
            panel_users_before: 'usersPanelBefore',
            panel_users_after: 'usersPanelAfter',
        };

        _.each(userPanelsMap, (name, key) => {
            if (!this[name]) {
                this[name] = this.getMetaPanel(key) || {fields: []};
            }
        });

        return this.usersPanel;
    },

    /**
     * Set the user template partial
     *
     * Defaults to 'user-single'
     */
    setUsersTemplate: function() {
        var panel = this.getUsersPanel();
        this.usersTemplate = panel && panel.template ? panel.template : 'user-single';
    },

    /**
     * Set user fields for hbs template
     */
    setUsersFields: function() {
        var panel = this.getUsersPanel();
        this.userField = _.find(panel.fields, function(field) {
            return field.name === 'created_by_name';
        });
        this.hasAvatarUser = !!this.userField;
    },

    /**
     * Set relevant metadata from the header panel
     *
     * The base card simply needs the panel itself
     */
    setHeaderPanel: function() {
        this.getHeaderPanel();
    },

    /**
     * Get and cache the header panel
     *
     * @return {Object}
     */
    getHeaderPanel: function() {
        if (!this.headerPanel) {
            this.headerPanel = this.getMetaPanel('panel_header');
        }

        return this.headerPanel;
    },

    /**
     * Get invitees user definitions
     */
    getInvitees: function() {
        var list = [];

        // if invitees is removed from preview/record layout, this will be empty
        var invitees = this.activity.get('invitees');

        if (invitees && invitees.models) {
            var panel = this.getUsersPanel();

            // as we are rendering each invitee avatar/name, we need the
            // singular name def from default fields metadata
            var def = _.find(panel.defaultFields, function(field) {
                return field.name === 'name';
            });

            if (!def) {
                return list;
            }

            _.each(invitees.models, _.bind(function(model) {
                var hasName = !!model.get('name');
                var userDef = {};

                if (hasName) {
                    userDef = {
                        userField: def,
                        userModel: model
                    };
                } else {
                    var email = model.get('email') ?
                        _.first(model.get('email')).email_address :
                        '';

                    if (email) {
                        userDef = {
                            userValue: email
                        };
                    }
                }

                if (userDef) {
                    list.push(userDef);
                }
            }, this));
        }

        return list;
    },

    /**
     * Determine if there are more invitees not fetched
     *
     * The activity model's invitees list is throttled by the max_num
     * system setting
     *
     * @return {boolean} true if there are more invitees, false otherwise
     */
    hasMoreInvitees: function() {
        var invitees = this.activity.get('invitees');
        var hasMore = false;

        if (invitees && invitees.offsets) {
            hasMore = _.some(invitees.offsets, function(offset) {
                return offset !== -1;
            });
        }

        return hasMore;
    },

    /**
     * Set invitees variables for hbs
     */
    setInvitees: function() {
        this.userList = this.getInvitees();
        this.hasMoreUsers = this.hasMoreInvitees();

        this.hasAvatarUser = !!this.userList;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.userList.splice(0, this.userList.length);

        this._super('_dispose');
    }
})
