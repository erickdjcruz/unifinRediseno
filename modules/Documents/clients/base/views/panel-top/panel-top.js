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
 * @class View.Views.Base.Documents.PanelTopView
 * @alias SUGAR.App.view.views.BaseDocumentsPanelTopView
 * @extends View.Views.Base.PanelTopView
 */
({
    extendsFrom: 'PanelTopView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = this.plugins || [];
        this.plugins.push('CloudDrive');
        this._super('initialize', arguments);
        this.addCloudButtons();
    },

    /**
     * Add cloud syncing buttons
     */
    addCloudButtons: function() {
        let dropdown = this._getPanelDropdown();

        dropdown.push({
            'type': 'rowaction',
            'event': 'button:sync_all_to_google:click',
            'name': 'sync_all_to_google',
            'label': 'LBL_SYNC_ALL_TO_GOOGLE_BUTTON_LABEL',
            'acl_action': 'view',
        }, {
            'type': 'rowaction',
            'event': 'button:sync_all_to_onedrive:click',
            'name': 'sync_all_to_onedrive',
            'label': 'LBL_SYNC_ALL_TO_ONEDRIVE_BUTTON_LABEL',
            'acl_action': 'view',
        }, {
            'type': 'rowaction',
            'event': 'button:sync_all_to_dropbox:click',
            'name': 'sync_all_to_onedrive',
            'label': 'LBL_SYNC_ALL_TO_DROPBOX_BUTTON_LABEL',
            'acl_action': 'view',
        });

        this.listenTo(this.context, 'button:sync_all_to_google:click', _.bind(this.syncToDrive, this, 'google'));
        this.listenTo(this.context, 'button:sync_all_to_onedrive:click', _.bind(this.syncToDrive, this, 'onedrive'));
        this.listenTo(this.context, 'button:sync_all_to_dropbox:click', _.bind(this.syncToDrive, this, 'dropbox'));
    },

    /**
     * Sync everything to drive
     *
     * @param string type
     */
    syncToDrive: function(type) {
        const driveDashlet = this._searchForDashlet(type);
        const driveDashletCid = driveDashlet.cid;
        const driveDashletPath = driveDashlet.pathFolders;

        if (!driveDashletCid) {
            app.alert.show('drive-error', {
                level: 'error',
                messages: app.lang.get('LBL_DRIVE_CLOUD_DASHLET_NOT_PRESENT'),
            });
            return false;
        }

        let cache = app.cache.get(driveDashletCid);
        const module = this.parentModule;
        const recordId = this.context.parent.get('modelId');
        let path = cache.folderId || 'root';

        const url = app.api.buildURL('CloudDrive', 'files/syncAll');

        app.alert.show('drive-syncing', {
            level: 'process'
        });

        app.api.call('create', url, {
            module: module,
            recordId: recordId,
            path: path,
            type: type,
            driveId: cache.driveId,
            folderPath: driveDashletPath,
        }, {
            success: _.bind(this.syncDriveDashlet, this, driveDashletCid),
            error: function(error) {
                app.alert.show('drive-error', {
                    level: 'error',
                    messages: error.message,
                });
            },
            complete: function() {
                app.alert.dismiss('drive-syncing');
            },
        });
    },

    /**
     * Get the dropdown for the panel-top
     */
    _getPanelDropdown: function() {
        if (_.has(this.meta, 'buttons') && _.has(this.meta.buttons[0], 'buttons')) {
            return this.meta.buttons[0].buttons;
        }
    },
})
