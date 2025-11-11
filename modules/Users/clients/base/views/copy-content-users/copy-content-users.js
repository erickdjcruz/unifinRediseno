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
 * @class View.Views.Base.UsersCopyContentUsers
 * @alias SUGAR.App.view.layouts.BaseUsersCopyContentUsersView
 * @extends View.Views.Base.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'change .destinationSelect': 'updateDestination',
    },

    /**
     * Updates destination users, teams. roles.
     *
     */
    updateDestination: function() {
        let users = this.getField('users_select').items;
        let teams = this.getField('teams_select').items;
        let roles = this.getField('roles_select').items;
        let destinationList = this.mergeDestinations(users, teams, roles);

        this.context.set({
            'destinationUsers': this.getDestinationIds(users),
            'destinationTeams': this.getDestinationIds(teams),
            'destinationRoles': this.getDestinationIds(roles),
            'destinationList': destinationList,
        });
    },

    /**
     * Returns array of destination names
     *
     * @param {Array} users
     * @param {Array} teams
     * @param {Array} roles
     */
    mergeDestinations: function(users, teams, roles) {
        let userNames = this.getDestinationNames(users);
        let teamNames = this.getDestinationNames(teams);
        let roleNames = this.getDestinationNames(roles);
        let destinationNames = [...userNames, ...teamNames, ...roleNames];

        return destinationNames;
    },

    /**
     * Filters the names from the destination list
     *
     * @param {Array} destinationList
     */
    getDestinationNames: function(destinationList) {
        let list = _.filter(destinationList, function(item) {
            return !_.isEmpty(item.id);
        });
        return _.map(list, function(destination) {
            return destination.text;
        });
    },

    /**
     * Filters the ids from the destination list
     *
     * @param {Array} destinationList
     */
    getDestinationIds: function(destinationList) {
        return _.map(destinationList, function(destination) {
            return destination.id;
        });
    },
});
