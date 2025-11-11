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
 * @class View.Views.Base.AdministrationDrivePathButtonsView
 * @alias SUGAR.App.view.views.BaseAdminstrationDrivePathButtonsView
 * @extends View.Views.Base.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click [name=save_button]': 'saveCurrentPath',
        'click [name=cancel_button]': 'closeDrawer',
        'click [name=shared_button]': 'toggleCheckbox',
        'change .sharedWithMe': 'toggleShared'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this.driveType = this.context.get('driveType');
        this.driveTypeLabel = app.lang.getAppListStrings('drive_types')[this.driveType];
    },

    _render: function() {
        this._super('_render');
        this._handleSharedWithMeButton();
    },

    /**
     * Save the current path
     *
     * @param {Event} evt
     */
    saveCurrentPath: function(evt) {
        let folders = this.layout.getComponent('drive-path-select').currentPathFolders;
        const folderId = this.layout.getComponent('drive-path-select').currentFolderId;
        const driveId = this.layout.getComponent('drive-path-select').driveId;
        const siteId = this.layout.getComponent('drive-path-select').siteId;

        const url = app.api.buildURL('CloudDrive', 'path');

        app.alert.show('path-processing', {
            level: 'process'
        });

        app.api.call('create', url, {
            isRoot: this.context.get('isRoot'),
            pathModule: this.context.get('pathModule'),
            type: this.driveType,
            drivePath: JSON.stringify(folders),
            folderId: folderId,
            driveId: driveId,
            siteId: siteId,
            isShared: this.context.get('sharedWithMe'),
            pathId: this.context.get('pathId'),
        } , {
            success: function() {
                app.alert.dismiss('path-processing');
                app.drawer.close();
            },
            error: function(error) {
                app.alert.show('cloud-error', {
                    level: 'error',
                    messages: error.message,
                });
            },
        });
    },

    /**
     * Close drawer
     *
     * @param {Event} evt
     */
    closeDrawer: function(evt) {
        app.drawer.close();
    },

    /**
     * Toggle between shard and My files
     *
     * @param {Event} evt
     */
    toggleShared: function(evt) {
        if (this.driveType === 'sharepoint') {
            return;
        }
        this.sharedWithMe = this.$('.sharedWithMe').prop('checked');
        this.context.set('sharedWithMe', this.sharedWithMe);
        let pathView = this.layout.getComponent('drive-path-select');
        pathView.loadFolders(null, this.sharedWithMe);
    },

    /**
     * Checkbox event
     *
     * @param {Event} evt
     */
    toggleCheckbox: function(evt) {
        const checkbox = this.$('.sharedWithMe');
        checkbox.prop('checked', !this.sharedWithMe);

        this.toggleShared();
    },

    /**
     * Disable the Shared with me button for Sharepoint
     *
     */
    _handleSharedWithMeButton: function() {
        if (this.driveType === 'sharepoint') {
            this.$('[name="shared_button"]').attr('disabled', true);
            this.$('[name="shared_button"]').addClass('disabled');
        }
    },
});
