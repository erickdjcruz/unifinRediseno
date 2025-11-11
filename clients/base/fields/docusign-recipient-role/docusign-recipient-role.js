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
 * @class View.Fields.Base.DocusignRecipientRoleField
 * @alias SUGAR.App.view.fields.BaseDocusignRecipientRoleField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options = this.setupItems(options);

        this._super('initialize', [options]);

        this._registerEvents();
    },

    /**
     * Setup dropdown items
     *
     * @param {Object} options
     * @return {Object}
     */
    setupItems: function(options) {
        options.def.options = {'': ''};

        const templateDetails = options.context.get('templateDetails') || {};

        let roles = templateDetails.roles || [];

        const rolesSelectedAlready = _.values(options.context.get('storedRoles'));
        if (!_.isUndefined(rolesSelectedAlready)) {
            roles = _.filter(roles, function(role) {
                return !_.includes(rolesSelectedAlready, role.name);
            });
        }

        const firstRole = _.first(roles);

        if (roles.length > 0 && !_.isUndefined(firstRole) && !_.isUndefined(firstRole.routing_order) &&
            firstRole.routing_order != '') {
            roles = _.sortBy(roles, 'routing_order');
        }

        _.each(roles, function setRoles(role) {
            options.def.options[role.name] = role.name;
        }, this);

        return options;
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.view.collection, 'data:sync:complete', this.setRoleConfiguredBefore);
        this.listenTo(this.context, 'updateRecipientItems', this.updateRecipientItems);

        this.listenTo(this.model, 'change:role', this.roleChanged);
    },

    /**
     * Role changed
     *
     * @param {Object} model
     */
    roleChanged: function(model) {
        if (this.view.name !== 'docusign-recipients-multi-selection-list') {
            return;
        }

        this._storeRoles(model);

        this.context.trigger('updateRecipientItems', model);
    },

    /**
     * Update recipient items
     *
     * @param {Object} model
     * @param {boolean} force
     */
    updateRecipientItems: function(model, force) {
        if (this.view.name !== 'docusign-recipients-multi-selection-list') {
            return;
        }

        if (this.model.cid === model.cid && !force) {
            return;
        }
        const rolesSelectedAlready = this._getRolesSelected();
        const defaultItemsList = this.context.get('templateDetails').roles;
        let availableRoles = {
            '': ''
        };
        _.each(defaultItemsList, function(item) {
            if (!_.includes(rolesSelectedAlready, item.name)) {
                availableRoles[item.name] = item.name;
            }
        });

        this.items = this._buildItems(availableRoles, rolesSelectedAlready);
    },

    /**
     * Build items
     *
     * @param {Object} availableRoles
     * @param {Array} rolesSelectedAlready
     * @return {Object}
     */
    _buildItems: function(availableRoles, rolesSelectedAlready) {
        if (_.isEmpty(this.model.get(this.name))) {
            return availableRoles;
        } else {
            const defaultItemsList = this.context.get('templateDetails').roles;

            //filter list but let the value already set in the list
            let availableRolesPlusCurrent = {
                '': ''
            };
            _.each(defaultItemsList, function(item) {
                if (_.includes(rolesSelectedAlready, item.name) && item.name !== this.model.get(this.name)) {
                    return;
                }

                availableRolesPlusCurrent[item.name] = item.name;
            }, this);

            return availableRolesPlusCurrent;
        }
    },

    /**
     * Get roles already selected
     *
     * @return {Array}
     */
    _getRolesSelected: function() {
        let rolesSelectedAlready = _.values(this.context.get('storedRoles'));
        if (_.isUndefined(rolesSelectedAlready)) {
            rolesSelectedAlready = [];
        }

        return rolesSelectedAlready;
    },

    /**
     * Store roles for when user changes the modules
     *
     * @param {Object} modelChanged Model changed
     */
    _storeRoles: function(modelChanged) {
        let storedRoles = this.context.get('storedRoles');
        if (_.isUndefined(storedRoles)) {
            storedRoles = {};
        }

        const currentRole = modelChanged.get(this.name);
        const recordId = modelChanged.get('id');

        delete storedRoles[recordId];

        if (!_.isEmpty(currentRole)) {
            storedRoles[recordId] = currentRole;
        }

        this.context.set('storedRoles', storedRoles);
    },

    /**
     * Set role configured before switch modules
     */
    setRoleConfiguredBefore: function() {
        if (this.view.name !== 'docusign-recipients-multi-selection-list') {
            return;
        }

        const storedRoles = this.context.get('storedRoles');
        const recipientId = this.model.get('id');
        if (!_.isUndefined(storedRoles) && !_.isUndefined(storedRoles[recipientId])) {
            this.model.set(this.name, storedRoles[recipientId], {silent: true});
            this.updateRecipientItems(this.model, true);

            this.render();
        }
    }
});
