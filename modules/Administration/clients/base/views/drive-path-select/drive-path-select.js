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
 * @class View.Views.Base.AdministrationDrivePathSelectView
 * @alias SUGAR.App.view.views.BaseAdminstrationDrivePathSelectView
 * @extends View.Views.Base.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click .folder': 'intoFolder',
        'click .setFolder': 'setFolder',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        this.context.on('change:sharedWithMe', this.updateCurrentFolderPaths, this);

        const sharedWithMe = this.layout.getComponent('drive-path-buttons').sharedWithMe;

        let rootName = app.lang.getAppString('LBL_MY_FILES');
        let sharedName = app.lang.getAppString('LBL_SHARED');

        this.currentPathFolders = sharedWithMe ? [
            {name: sharedName, folderId: 'root', sharedWithMe: true},
        ] : [
            {name: rootName, folderId: 'root'},
        ];

        this.pathIds = ['root'];
        this.driveType = this.context.get('driveType');

        if (this.driveType === 'sharepoint') {
            this.pathIds = [];
            const sitesName = app.lang.getAppString('LBL_SITES');
            this.currentPathFolders = [
                {name: sitesName, folderId: null, resourceType: 'site'},
            ];
        }

        this.loadFolders();
    },

    /**
     * reset the paths folders
     *
     * @param {Context} context
     */
    updateCurrentFolderPaths: function(context) {
        if (context.get('sharedWithMe')) {
            this.currentPathFolders = [{name: 'Shared', folderId: 'root', sharedWithMe: true},];
        } else {
            this.currentPathFolders = [{name: 'My files', folderId: 'root'},];
        }
    },

    /**
     * Load folders from the drive
     *
     * @param {string} currentFolderId
     * @param {boolean} sharedWithMe
     * @param {string} driveId Used for onedrive navigation
     */
    loadFolders: function(currentFolderId, sharedWithMe, driveId) {
        this.currentFolderId = currentFolderId || this.context.get('parentId');
        const limit = 100;
        const url = app.api.buildURL('CloudDrive', 'list/folders');

        app.alert.show('folders-loading', {
            level: 'process'
        });

        let parentId = this.currentFolderId;

        if (this.driveType === 'sharepoint' && (this.displayingDocumentDrives || this.displayingSites)) {
            parentId = null;
        }

        let options = {
            parentId: parentId,
            sharedWithMe: sharedWithMe || false,
            driveId: driveId,
            type: this.driveType,
            folderPath: this.currentPathFolders,
            limit: limit,
            siteId: this.siteId,
        };

        app.api.call('create', url, options, {
            success: _.bind(function(result) {
                app.alert.dismiss('folders-loading');
                this.folders = result.files;
                this.displayingSites = result.displayingSites;
                this.displayingDocumentDrives = result.displayingDocumentDrives;
                this.render();
            }, this),
            error: function(error) {
                app.alert.show('drive-error', {
                    level: 'error',
                    messages: error.message,
                });
            },
        });
    },

    /**
     * Steps into a folder
     *
     * @param {Event} evt
     */
    intoFolder: function(evt) {
        if (this.driveType === 'sharepoint') {
            this.intoSharepointFolder(evt);
            return;
        }
        let event = evt.target.dataset;
        const currentFolderId = event.id;
        const currentFolderName = event.name;
        const driveId = event.driveid || null;

        const sharedWithMe = this.layout.getComponent('drive-path-buttons').sharedWithMe;

        if (evt.target.classList.contains('back')) {
            this.currentPathFolders.pop();
            this.pathIds.pop();
        } else {
            this.currentPathFolders.push({name: currentFolderName, folderId: currentFolderId, driveId: driveId});
            this.pathIds.push(currentFolderId);
        }

        this.driveId = driveId;
        this.currentFolderName = currentFolderName;

        this.parentId = this.pathIds[this.pathIds.length - 2];

        this.loadFolders(currentFolderId, sharedWithMe, driveId);
    },

    /**
     * Special handler for Sharepoint navigation
     *
     * @param {Event} evt
     */
    intoSharepointFolder: function(evt) {
        let event = evt.target.dataset;
        let isSite = event.site;
        let isDocumentDrive = event.documentlibrary;
        let resourceId = event.id;
        const resourceName = event.name;
        const resourceType = this.getSharePointResourceType(isSite, isDocumentDrive);

        if (evt.target.classList.contains('back')) {
            this.currentPathFolders.pop();
            let lastPath = this.currentPathFolders[this.currentPathFolders.length - 1];
            if (lastPath) {
                if (lastPath.resourceType === 'site') {
                    isSite = true;
                    this.displayingSites = true;
                }
                if (lastPath.resourceType === 'drive') {
                    isDocumentDrive = true;
                    this.displayingDocumentDrives = true;
                }

                resourceId = lastPath.id;
            } else {
                isSite = true;
                isDocumentDrive = false;
            }

        } else {
            this.currentPathFolders.push({
                name: resourceName,
                id: resourceId,
                resourceType: resourceType,
            });
        }

        if (isSite) {
            this.siteId = resourceId;
            this.driveId = null;
            this.loadFolders(resourceId, null, null);
        } else if (isDocumentDrive) {
            this.driveId = resourceId;
            this.loadFolders(resourceId, null, this.driveId);
        } else {
            this.loadFolders(resourceId, null, this.driveId);
        }
    },

    /**
     * Gets the resource type
     *
     * @param {bool} isSite
     * @param {bool} isDocumentDrive
     * @return string
     */
    getSharePointResourceType: function(isSite, isDocumentDrive) {
        if (isSite) {
            return 'site';
        }

        if (isDocumentDrive) {
            return 'drive';
        }

        return 'folder';
    },

    /**
     * Sets a folder as the current folder
     *
     * @param {Event} evt
     */
    setFolder: function(evt) {
        let folders = this.currentPathFolders;
        const folderId = evt.target.dataset.id;
        const folderName = evt.target.dataset.name;
        let driveId = evt.target.dataset.driveid;

        let isSite = evt.target.dataset.site;
        let isDocumentDrive = evt.target.dataset.documentlibrary;
        const resourceType = this.getSharePointResourceType(isSite, isDocumentDrive);

        if (_.isArray(folders)) {
            if (this.driveType === 'sharepoint') {
                if (isSite) {
                    this.siteId = folderId;
                }
                if (isDocumentDrive) {
                    this.driveId = folderId;
                    driveId = this.driveId;
                }
                folders.push({
                    name: folderName,
                    id: folderId,
                    resourceType: resourceType,
                });
            } else {
                folders.push({folderId: folderId, name: folderName, driveId: driveId,});
            }
        }

        const url = app.api.buildURL('CloudDrive', 'path');

        app.alert.show('path-processing', {
            level: 'process'
        });

        app.api.call('create', url, {
            pathModule: this.context.get('pathModule'),
            isRoot: this.context.get('isRoot'),
            type: this.driveType,
            drivePath: JSON.stringify(folders),
            folderId: folderId,
            driveId: driveId,
            siteId: this.siteId,
            isShared: this.context.get('sharedWithMe'),
            pathId: this.context.get('pathId'),
        } , {
            success: _.bind(function() {
                app.alert.dismiss('path-processing');
                app.drawer.close();
            }),
            error: function(error) {
                app.alert.show('drive-error', {
                    level: 'error',
                    messages: error.message,
                });
            }
        });
    },
});
