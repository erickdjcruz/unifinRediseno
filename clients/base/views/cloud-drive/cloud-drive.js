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
 * @class View.Views.Base.CloudDriveView
 * @alias SUGAR.App.view.views.BaseCloudDriveView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click .folder': 'intoFolder',
        'click .parentFolder': 'intoFolder',
        'click .file': 'previewFile',
        'click .loadmore': 'loadMore',
        'click .downloadFile': 'downloadFile',
        'click .deleteFile': 'deleteFile',
        'click .createSugarDocument': 'createSugarDocument',
        'click .createFolder': 'createFolder',
        'click .sorting': 'sortColumn',
        'mouseenter [data-toggle=tooltip]': 'showTooltip',
        'mouseleave [data-toggle=tooltip]': 'hideTooltip',
        'click .copyLink': 'copyLink',
        'click .sendToDocuSign': 'downloadDocumentInSugar',
    },

    /**
     * @inheritdoc
     */
    plugins: ['Dashlet', 'DocumentMerge'],

    /**
     * Default drive type
     */
    _defaultDriveType: 'google',


    /**
     * Flag to indicate if the toolbar was updated
     */
    _dashletToolbarSet: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this.parentIds = ['root'];

        const ctxModel = app.controller.context;
        this.showMergeButtonsOnThisView = ctxModel.get('layout') === 'record' && ctxModel.get('module') !== 'Home';

        app.events.on(`${this.cid}:cloud-drive:reload`, this.loadFiles, this);
        $(window).on('resize.' + this.cid, _.bind(_.debounce(this.adjustHeaderPaneTitle, 50), this));
    },

    /**
     * Init dashle settings
     */
    initDashlet: function() {
        this.options.driveType = this.settings.get('drive_type') || this._defaultDriveType;
        app.cache.set(this.cid, {
            driveType: this.options.driveType,
        });

        let rootName = app.lang.getAppString('LBL_MY_FILES');
        let sharedName = app.lang.getAppString('LBL_SHARED');
        let rootId = 'root';

        if (this.options.driveType === 'sharepoint') {
            rootName = app.lang.getAppString('LBL_SITES');
            rootId = null;
        }

        this.persistentSettings = {
            shared: {
                folderId: rootId,
                path: [{name: sharedName, folderId: rootId, sharedWithMe: true},],
                parentId: null,
                driveId: null,
            },
            regular: {
                folderId: rootId,
                path: [{name: rootName, folderId: rootId},],
                parentId: null,
                driveId: null,
                resourceType: 'site',
            }
        };

        this._defaultRootFolder = this.sharedWithMe ?  [
            {name: sharedName, folderId: rootId, sharedWithMe: true},
        ] : [
            {name: rootName, folderId: rootId, resourceType: 'site'},
        ];

        this.getRootFolder();

        if (!app.acl.hasAccess('create', 'Documents')) {
            this.noDocumentsAccess = true;
        }
    },

    /**
     * Adds the events for the buttons inside the popover
     *
     * @param {Event} evt
     */
    addPopoverEvents: function(evt) {
        //The popover is not rendered on this.$el, have to use global selection
        $('.createFolderBtn').off('click');
        $('.createFolderBtn').on('click', _.bind(this.createNewFolder, this));
        $('.uploadFileBtn').off('click');
        $('.uploadFileBtn').on('click', _.bind(this.uploadNewFile, this));

        $('body').on(`click.${this.cid}`, _.bind(this.closeOnOutsideClick, this));
        this.popover = true;
    },

    /**
     * Closes the popover when user clcks outside it
     *
     * @param {Event} evt
     */
    closeOnOutsideClick: function(evt) {
        const dashlet = this.$el.closest('.dashlet');

        //Using global jquery since the popover is not generated on this.$el
        if ($(evt.target).closest('.popover').length === 0) {
            this.hidePopover();
        }
        dashlet.find('.newFolder').removeClass('active');
        dashlet.find('.uploadFile').removeClass('active');

        return;
    },

    /**
     * Removes the events added for the buttons inside the popover
     *
     * @param {Event} evt
     */
    removePopoverEvents: function(evt) {
        $('.createFolderBtn').off();
        $('.uploadFileBtn').off();
        $('body').off(`click.${this.cid}`);
        this.popover = false;
    },

    /**
     * Load dashlet files
     *
     * @param {Function} callback
     * @param {bool} isRefresh
     */
    loadFiles: function(callback, isRefresh) {
        callback = _.isFunction(callback) ? callback : this.displayItems;

        if (!this.folderId) {
            this.showCreateMessage = true;
            this.render();
            return;
        }

        if ((this.folderId === 'root') &&
            (this.options.driveType !== 'sharepoint' ||
                (this.options.driveType === 'sharepoint' && _.isUndefined(isRefresh)))) {
            this.driveId = null;
        }

        if (this.folderId === this.driveId) {
            this.folderId = 'root';
        }

        this._updateDashletCache({
            folderId: this.folderId,
            driveId: this.driveId,
        });

        const url = app.api.buildURL('CloudDrive/list', 'files');
        app.alert.show('drive-loading', {
            level: 'process'
        });

        let nextPageToken = isRefresh ? null : this.nextPageToken;

        app.api.call('create', url, {
            folderId: this.folderId,
            nextPageToken: nextPageToken,
            sharedWithMe: this.sharedWithMe,
            type: this.options.driveType,
            driveId: this.driveId,
            sortOptions: this.sortOptions,
            folderPath: this.pathFolders,
            siteId: this.siteId,
        }, {
            success: _.bind(callback, this),
            error: _.bind(this._handleDriveError, this),
            complete: function() {
                app.alert.dismiss('drive-loading');
            },
        });
    },

    /**
     * Gets some data for displaying
     *
     * @param {Object} data
     */
    displayItems: function(data) {
        if (!_.isUndefined(data.success) && !data.success) {
            this.noConnection = true;
            this.errorMessage = data.message;
        }

        this.showCreateMessage = false;
        this.files = data.files;
        this.nextPageToken = data.nextPageToken;
        this.displayingSites = data.displayingSites;
        this.displayingDocumentDrives = data.displayingDocumentDrives;
        this.render();
    },

    /**
     * Get the root context id for the current context
     *
     * @param {Function} callback
     */
    getRootFolder: function() {
        app.alert.dismiss('drive-syncing');
        app.alert.show('drive-loading', {
            level: 'process'
        });
        this.loading = true;
        /**
         * From the drivePaths module we will get a folderId or a folderName
         * if we get a folderId just set it on this.folderId and call calback
         * otherwise we need to search for folder and get it's id
         */
        const url = app.api.buildURL('CloudDrive', 'path', null, {
            module: this.module,
            recordId: this.model.get('id'),
            type: this.options.driveType,
            layoutName: app.controller.context.get('layout'),
        });

        app.api.call('read', url, null, {
            success: _.bind(function(result) {
                if (result.success === false) {
                    this.noConnection = true;
                    this.errorMessage = result.message;
                    this.render();
                } else {
                    this.noConnection = false;
                    this.folderId = result.root;
                    this.sharedWithMe = result.isShared ? true : false;

                    if (_.isString(result.path)) {
                        result.path = JSON.parse(result.path);
                    }
                    if (this.options.driveType === 'sharepoint') {
                        result.path = this.convertToSharepointPath(result.path);
                    }

                    this.pathFolders = result.path || this._defaultRootFolder;

                    this.pathCreateIndex = result.pathCreateIndex;
                    this.nextPageToken = result.nextPageToken;
                    this.parentId = result.parentId;
                    this.driveId = result.driveId;
                    this.siteId = result.siteId;
                    this.setPersistentSettings();
                    this.loadFiles();
                }
            }, this),
            error: _.bind(this._handleDriveError, this),
            complete: _.bind(function() {
                app.alert.dismiss('drive-loading');
                this.loading = false;
                this.render();
            }, this),
        });
    },

    /**
     * steps into folder
     *
     * @param {Event} evt
     */
    intoFolder: function(evt) {
        if (this.options.driveType === 'sharepoint') {
            this.intoSharePointFolder(evt);
            return;
        }

        if (evt.target.dataset.id) {
            this.folderId = evt.target.dataset.id;
            this.driveId = evt.target.dataset.driveid;
        }

        if (evt.target.classList.contains('back')) {
            let parentIdsRemoveIndex = this.parentIds.indexOf(this.folderId);
            this.parentIds.splice(parentIdsRemoveIndex + 1);

            const pathRemoveIndex = evt.target.dataset.index;
            this.pathFolders.splice(parseInt(pathRemoveIndex) + 1);
        } else {
            this.pathFolders.push({
                name: evt.target.text,
                folderId: this.folderId,
                driveId: this.driveId,
            });
            this.parentIds.push(this.folderId);
        }
        this.setPersistentSettings();

        this.parentId = this.parentIds[this.parentIds.length - 2];
        this.nextPageToken = null;

        this.options.driveType === 'dropbox' ? this.loadFiles() : this.getParent(this.navigateTo);
    },

    /**
     * Special handler for Sharepoint
     *
     * @param {Event} evt
     */
    intoSharePointFolder: function(evt) {
        let event = evt.target.dataset;
        let isSite = event.site;
        let isDocumentDrive = event.documentlibrary;
        let resourceId = event.id;
        const resourceName = event.name;
        const resourceType = this.getSharePointResourceType(isSite, isDocumentDrive);

        this.folderId = resourceId;

        if (evt.target.classList.contains('back')) {
            const pathRemoveIndex = event.index;
            this.pathFolders.splice(parseInt(pathRemoveIndex) + 1);
            if (this.pathFolders.length === 0) {
                resourceId = null;
            }
        } else {
            this.pathFolders.push({
                name: resourceName,
                folderId: resourceId,
                resourceType: resourceType,
            });
        }
        this.setPersistentSettings();

        if (isSite) {
            this.siteId = resourceId;
            this.folderId = 'root';
            this.driveId = null;
        }
        if (isDocumentDrive) {
            this.driveId = resourceId;
        }

        this.loadFiles(null, true);
    },

    /**
     * Converts paths to sharepoint format
     *
     * @param {Array} paths
     * @return null|array
     */
    convertToSharepointPath: function(paths) {
        if (_.isEmpty(paths)) {
            return paths;
        }
        return _.map(paths, (path) => {
            return _.extend(path, {folderId: path.id});
        });
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
     * Sets persistent settings for local/shared paths
     */
    setPersistentSettings: function() {
        if (this.sharedWithMe) {
            this.persistentSettings.shared = _.assign({}, {
                folderId: this.folderId,
                path: this.pathFolders,
                parentId: this.parentId,
                driveId: this.driveId
            });
        } else {
            this.persistentSettings.regular = _.assign({}, {
                folderId: this.folderId,
                path: this.pathFolders,
                parentId: this.parentId,
                driveId: this.driveId
            });
        }
    },

    /**
     * Gets the persistent settings for local/shared paths
     *
     * @param {bool} sharedWithMe
     */
    getPersistentSettings: function(sharedWithMe) {
        if (sharedWithMe) {
            return this.persistentSettings.shared;
        }

        return this.persistentSettings.regular;
    },

    /**
     * Navigate inside a folder
     *
     * @param {string} file
     */
    navigateTo: function(file) {
        this.parentId = this.parentId === 'root' ?
            'root' : file && file.parents && file.parents.length ?
                file.parents[0] : this.parentId;
        this.files = [];

        const lastOffset = 2;
        let lastPaths = this.pathFolders.slice(this.pathFolders.length - lastOffset);

        if (_.isArray(lastPaths) && _.isUndefined(lastPaths[0].folderId)) {
            lastPaths[0].folderId = this.parentId;
            lastPaths[0].driveId = this.driveId;
        }
        this.loadFiles();
    },

    /**
     * Retrieves parent id
     *
     * @param {Function} callback
     */
    getParent: function(callback) {
        const url = app.api.buildURL('CloudDrive/file', this.folderId, null, {
            type: this.options.driveType,
            driveId: this.driveId
        });
        app.api.call('read', url, null, {
            success: _.bind(callback, this),
            error: _.bind(this._handleDriveError, this),
        });
    },

    /**
     * toggle the "Shared With Me" option
     *
     * @param {Event} evt
     */
    toggleShared: function(evt) {
        this.sharedWithMe = evt.target.dataset.sharedwithme === 'true';
        this.nextPageToken = null;
        this.files = [];
        const persistentSettings = this.getPersistentSettings(this.sharedWithMe);
        this.folderId = persistentSettings.folderId;
        this.pathFolders = persistentSettings.path;
        this.parentId = persistentSettings.parentId;
        this.driveId = persistentSettings.driveId;
        this.sortOptions = null;
        this.loadFiles();
    },

    /**
     * Retrieves file view link
     *
     * @param {Event} evt
     */
    previewFile: function(evt) {
        const fileId = evt.target.dataset.id;
        const webViewLink = evt.target.dataset.link;
        if (webViewLink) {
            this.showPreview({webViewLink: webViewLink});
        } else {
            if (this.options.driveType === 'dropbox') {
                const folderName = evt.target.dataset.name;
                const folderPath = this.getFolderPath(folderName);
                const url = app.api.buildURL('CloudDrive/shared', 'link');

                app.api.call('create', url, {
                    'type': this.options.driveType,
                    'folderPath': folderPath,
                }, {
                    success: _.bind(this.showPreview, this),
                    error: _.bind(this._handleDriveError, this),
                });
            } else {
                const url = app.api.buildURL('CloudDrive/file', fileId, null, {type: this.options.driveType});
                app.api.call('read', url, null, {
                    success: _.bind(this.showPreview, this),
                    error: _.bind(this._handleDriveError, this),
                });
            }
        }
    },

    /**
     * Shows file preview
     *
     * @param {string} file
     */
    showPreview: function(file) {
        const url = file.webViewLink || file.url;
        window.open(url, '_blank');
    },

    /**
     * Load more files
     *
     * @param {Event} evt
     */
    loadMore: function(evt) {
        this.loadFiles(this.appendData, false);
    },

    /**
     * Append files to existing
     *
     * @param {Array} data
     */
    appendData: function(data) {
        if (!_.isArray(this.files)) {
            this.files = _.values(this.files);
        }

        this.files.push(...data.files);
        this.nextPageToken = data.nextPageToken;
        this.render();
    },

    /**
     * Download a file from drive
     *
     * @param {Event} evt
     */
    downloadFile: function(evt) {
        const fileId = evt.target.dataset.id;
        const driveId = evt.target.dataset.driveid;
        const downloadUrl = evt.target.dataset.downloadurl;
        const folderName = evt.target.dataset.name;
        const folderPath = this.getFolderPath(folderName);

        if (!_.isEmpty(downloadUrl)) {
            window.open(downloadUrl, '_blank');
            return;
        }

        const file = _.filter(this.files, function(item) {return item.id === fileId;})[0];
        const fileName = file.name || 'unknown';

        app.alert.show('drive-syncing', {
            level: 'process'
        });
        const url = app.api.buildURL('CloudDrive/download');
        app.api.call('create', url, {
            fileId: fileId,
            driveId: driveId,
            type: this.options.driveType,
            folderPath: folderPath,
        }, {
            success: _.bind(function(data) {
                if (data.success) {
                    this.downloadFileLocally(fileName, data.usableMimeType, data.content);
                } else {
                    app.alert.show('drive-error-download', {
                        level: 'error',
                        title: app.lang.get('LBL_DRIVE_UNABLE_TO_DOWNLOAD')
                    });
                }
            }, this),
            error: _.bind(this._handleDriveError, this),
            complete: function() {
                app.alert.dismiss('drive-syncing');
            }
        });
    },

    /**
     * Downloads a file on the file system
     *
     * @param {string} filename
     * @param {string} fileType
     * @param {string} content
     */
    downloadFileLocally: function(filename, fileType, content) {
        const dataURIToBlob = function(dataURI) {
            let binStr = atob(dataURI);
            let len = binStr.length;
            let arr = new Uint8Array(len);

            for (let i = 0; i < len; i++) {
                arr[i] = binStr.charCodeAt(i);
            }

            return new Blob([arr], {
                type: fileType
            });
        };
        const blob = dataURIToBlob(content);
        const url = URL.createObjectURL(blob);

        let element = document.createElement('a');
        element.setAttribute('href', url);
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    },

    /**
     * Handles drive errors
     *
     * @param {Object} error
     * @param {string} actionType
     */
    _handleDriveError: function(error, actionType) {
        if (this.popover) {
            this.hidePopover();
        }

        const alertId = App.utils.generateUUID();
        let errorAlert = function(errorType, errorMessage) {
            app.alert.show(errorType + alertId, {
                level: 'error',
                messages: errorMessage
            });
        };

        if (_.isUndefined(actionType)) {
            errorAlert('drive-error', error.message);
        } else {
            if (error.message.includes('Access denied')) {
                switch (actionType) {
                    case 'deleteFile':
                        message = app.lang.get('LBL_NO_PERMISSION_FILE_ERROR');
                        break;
                    case 'deleteFolder':
                        message = app.lang.get('LBL_NO_PERMISSION_FOLDER_ERROR');
                        break;
                    case 'createFolder':
                        message = app.lang.get('LBL_NO_PERMISSION_FOLDER_CREATE_ERROR');
                        break;
                    case 'uploadFile':
                        message = app.lang.get('LBL_NO_PERMISSION_FILE_UPLOAD_ERROR');
                        break;
                    default:
                        message = app.lang.get('LBL_PERMISSION_ERROR');
                }
                errorAlert('drive-permission-error', message);
            } else {
                errorAlert('drive-error', error.message);
            }
        }

        this.render();
    },

    /**
     * Handles no permission errors
     *
     * @param {Object} error
     */
    _handleNoPermissionError: function(error) {
        if (this.popover) {
            this.hidePopover();
        }

        const alertId = app.utils.generateUUID();
        app.alert.show('drive-permission-error' + alertId, {
            level: 'error',
            messages: app.lang.get('LBL_NO_PERMISSION_FILE_ERROR'),
        });
        this.render();
    },

    /**
     * Deletes a file from drive
     *
     * @param {Event} evt
     */
    deleteFile: function(evt) {
        app.alert.show('drive_delete', {
            level: 'confirmation',
            messages: app.lang.get('LBL_DRIVE_DELETE_CONFIRM'),
            autoClose: false,
            onConfirm: () => {
                this._deleteFile(evt);
            },
        });
    },

    /**
     * Deletes a file from drive
     *
     * @param {Event} evt
     */
    _deleteFile: function(evt) {
        const fileId = evt.target.dataset.id;
        const driveId = evt.target.dataset.driveid;
        const folderName = evt.target.dataset.name;
        const fileType = evt.target.dataset.type;
        const folderPath = this.getFolderPath(folderName);

        app.alert.show('drive-syncing', {
            level: 'process'
        });

        const url = app.api.buildURL('CloudDrive/delete');
        app.api.call('create', url, {
            fileId: fileId,
            driveId: driveId,
            type: this.options.driveType,
            folderPath: folderPath,
        }, {
            error: (error) => {
                this._handleDriveError(error, fileType === 'file' ? 'deleteFile' : 'deleteFolder');
            },
            complete: () => {
                this.loadFiles(null, true);
                app.alert.dismiss('drive-syncing');
            },
        });
    },

    /**
     * Creates a document in sugar
     *
     * @param {Event} evt
     */
    createSugarDocument: function(evt) {
        const fileId = evt.target.dataset.id;
        const fileName = evt.target.dataset.filename;
        const driveId = evt.target.dataset.driveid;
        const recordId = this.model.get('id');
        const recordModule = this.model.get('_module');

        const url = app.api.buildURL('CloudDrive/createSugarDocument');
        app.api.call('create', url, {
            fileId: fileId,
            fileName: fileName,
            recordModule: recordModule,
            recordId: recordId,
            driveId: driveId,
            type: this.options.driveType
        }, {
            success: _.bind(function(result) {
                if (!result.success) {
                    app.alert.show('drive-error', {
                        level: 'error',
                        messages: app.lang.get(result.message),
                    });

                    return;
                }

                app.alert.show('drive-syncing', {
                    level: 'success',
                    messages: app.lang.get('LBL_DRIVE_DOCUMENT_CREATED'),
                });

                if (this.context.get('layout') === 'record') {
                    this.context.trigger('subpanel:reload', {
                        links: ['documents']
                    });
                }

                this.trigger('sugar-document:created', {
                    id: result.documentId
                });
            }, this),
            error: _.bind(this._handleDriveError, this),
        });
    },

    /**
     * Create a folder on the drive
     *
     * @param {Event} evt
     */
    createFolder: function(evt) {
        if (_.isArray(this.pathFolders) && (this.options.driveType === 'dropbox' || !_.isEmpty(this.parentId))) {
            let parentFolderId = this.parentId || this.folderId;

            if (this.pathCreateIndex === this.pathFolders.length) {
                this.folderId == this.parentId;
                this.parentId = this.oldParentId;
                this.showCreateMessage = false;

                if (this.options.driveType === 'onedrive') {
                    app.alert.show('drive-syncing', {
                        level: 'process',
                        title: app.lang.get('LBL_MICROSOFT_DELAY'),
                    });
                    setTimeout(_.bind(this.getRootFolder, this), 20000);
                } else {
                    this.getRootFolder();
                }

                return;
            }
            const folder = _.filter(this.pathFolders, function(item) {
                return item.name;
            })[this.pathCreateIndex];
            if (this.options.driveType === 'dropbox' || (!_.isUndefined(folder) && _.isString(folder.name))) {
                const url = app.api.buildURL('CloudDrive', 'folder');
                app.api.call('create', url, {
                    'name': folder.name,
                    'parent': parentFolderId,
                    'driveId': this.driveId,
                    'type': this.options.driveType,
                    'folderPath': this.pathFolders,
                }, {
                    success: _.bind(function(result) {
                        this.pathCreateIndex++;
                        this.oldParentId = this.parentId;
                        this.parentId = result.id;
                        this.driveId = result.driveId || this.driveId;
                        this.createFolder();
                    }, this),
                    error: _.bind(this._handleDriveError, this),
                    complete: function() {}
                });
            } else {
                this.folderId == this.parentId;
                this.parentId = this.oldParentId;
                this.showCreateMessage = false;
                this.getRootFolder();
            }
        }
    },

    /**
     * Refresh the dashlet
     *
     * @param {Event} evt
     */
    refreshPath: function(evt) {
        this.sortOptions = null;
        this.loadFiles(null, true);
    },

    getFolderPath: function(folderName) {
        return _.union(this.pathFolders, [
            {
                'name': folderName,
            }
        ]);
    },

    /**
     * Creates a new folder on the drive
     *
     * @param {Event} evt
     */
    createNewFolder: function(evt) {
        const folderName = $('[name=folderName]').val();
        const url = app.api.buildURL('CloudDrive', 'folder');
        const folderPath = this.getFolderPath(folderName);

        app.alert.show('drive-create-folder', {
            level: 'process'
        });

        app.api.call('create', url, {
            'name': folderName,
            'parent': this.folderId,
            'driveId': this.driveId,
            'type': this.options.driveType,
            'folderPath': folderPath,
        }, {
            success: () => {
                this.loadFiles(null, true);
            },
            error: (error) => {
                this._handleDriveError(error, 'createFolder');
            },
            complete: () => {
                app.alert.dismiss('drive-create-folder');
                this.hidePopover();
            },
        });
    },

    /**
     * Uploads a file on the drive
     * @param {Event} evt
     */
    uploadNewFile: function(evt) {
        const element = _.first($('input[name=uploadFile]'));
        const file = _.first(element.files);

        if (!file) {
            this.hidePopover();
            return;
        }

        const folderPath = this.getFolderPath(file.name);
        let formData = new FormData();
        formData.append('file', file);
        formData.append('fileName', file.name);
        formData.append('parentId', this.folderId);
        formData.append('type', this.options.driveType);
        formData.append('folderPath', JSON.stringify(folderPath));

        if (!_.isEmpty(this.driveId)) {
            formData.append('driveId', this.driveId);
        }

        const url = app.api.buildURL('CloudDrive', 'file');

        app.alert.show('drive-upload', {
            level: 'process'
        });

        app.api.call('create', url, formData, {
            success: _.bind(function(result) {
                app.alert.show('upload-success', {
                    level: 'info',
                    messages: app.lang.get(result.message),
                    autoClose: true,
                    autoCloseDelay: '15000',
                });
                this.loadFiles(null, true);
            }, this),
            error: (error) => {
                this._handleDriveError(error, 'uploadFile');
            },
            complete: () => {
                app.alert.dismiss('drive-upload');
                this.hidePopover();
            },
        }, {
            contentType: false,
            processData: false
        });
    },

    /**
     * Hides the popover
     */
    hidePopover: function() {
        const dashlet = this.$el.closest('.dashlet');

        const $popover = dashlet.find('[rel=popover]');
        if ($popover.length) {
            $popover.popover('hide');
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render', arguments);

        if (!this._dashletToolbarSet) {
            this._setupDashletToolbar();
        }

        this._addStyleOnTitle();
        this.addKebabButton();
        this.initPopovers();
        this.adjustDropdowns();
    },

    /**
     * Setup Cloud Drive buttons on dashlet toolbar
     */
    _setupDashletToolbar: function() {
        const dashlet = this.$el.closest('.dashlet');
        const dashletTitle = dashlet.find('.dashlet-title');
        const rightDashletButtons = dashlet.find('.btn-toolbar');

        if (rightDashletButtons.length !== 1) {
            return;
        }

        const toolbarTemplate = app.template.getView('cloud-drive', 'toolbar-buttons');
        const cloudDriveButtons = $(toolbarTemplate(this));

        cloudDriveButtons.insertAfter(dashletTitle);

        dashlet.find('.refreshPath').off();
        dashlet.find('.refreshPath').on('click', _.bind(this.refreshPath, this));

        if (this.options.driveType !== 'sharepoint') {
            dashlet.find('.toggleShared').off();
            dashlet.find('.toggleShared').on('click', _.bind(this.toggleShared, this));
        }

        this._dashletToolbarSet = true;
    },

    /**
     * Add style on title
     */
    _addStyleOnTitle: function() {
        const dashlet = this.$el.closest('.dashlet');
        const dashletTitle = dashlet.find('.dashlet-title');

        dashletTitle.toggleClass('ellipsis_inline', true);
    },

    /**
     * Add kebab button
     */
    addKebabButton: function() {
        const dashletToolbar = this.layout.getComponent('dashlet-toolbar');
        const kebabTemplate = app.template.getView('cloud-drive', 'kebab-actions');
        const kebabElement = kebabTemplate({showMergeButtonsOnThisView: this.showMergeButtonsOnThisView});

        if (dashletToolbar instanceof app.view.View) {
            if (dashletToolbar.$('.kebab-actions').length === 0) {
                dashletToolbar.$('.btn-toolbar > .dashlet-toolbar').after(kebabElement);
                dashletToolbar.$('.btn-toolbar').css('display', 'contents');

                dashletToolbar.$('a[data-dashletaction="newSignedDocument"]').on(
                    'click',
                    _.bind(this.newSignedDocument, this)
                );
                if (this.showMergeButtonsOnThisView) {
                    dashletToolbar.$('a[data-dashletaction="mergeDocuSign"]').on(
                        'click',
                        _.bind(this.docMergeAndSendToDocuSign, this)
                    );
                    dashletToolbar.$('a[data-dashletaction="mergeWEP"]').on(
                        'click',
                        _.bind(this.mergeFile, this)
                    );
                    dashletToolbar.$('a[data-dashletaction="mergePdf"]').on(
                        'click',
                        _.bind(this.mergeFile, this)
                    );
                }
            } else {
                const itemsOptions = [
                    {
                        dashletAction: 'newSignedDocument',
                        nonDocTooltip: app.lang.get('LBL_NO_SEND_TO_DOCUSIGN'),
                        docTooltip: app.lang.get('LBL_SEND_TO_DOCUSIGN')
                    },
                    {
                        dashletAction: 'mergeWEP',
                        nonDocTooltip: app.lang.get('LBL_NO_SEND_TO_DOCMERGE_WEP'),
                        docTooltip: ''
                    },
                    {
                        dashletAction: 'mergeDocuSign',
                        nonDocTooltip: app.lang.get('LBL_NO_SEND_TO_DOCMERGE_DS'),
                        docTooltip: ''
                    },
                    {
                        dashletAction: 'mergePdf',
                        nonDocTooltip: app.lang.get('LBL_NO_SEND_TO_DOCMERGE_PDF'),
                        docTooltip: ''
                    },
                ];
                this._updateAttributesAndTooltips(dashletToolbar, itemsOptions);
            }
        }
    },

    /**
     * Update attributes and tooltips of the list items
     *
     * @param {Object} dashletToolbar
     * @param {Array} itemsOptions
     */
    _updateAttributesAndTooltips: function(dashletToolbar, itemsOptions) {
        const isDisplayingNonDocumentPlace = (this.options.driveType === 'sharepoint') &&
            (this.displayingSites || this.displayingDocumentDrives);

        _.each(itemsOptions, function(itemOptions) {
            const tooltipText = isDisplayingNonDocumentPlace ?
                itemOptions.nonDocTooltip : itemOptions.docTooltip;
            const buttonSelector = `a[data-dashletaction="${itemOptions.dashletAction}"]`;

            dashletToolbar.$(buttonSelector).toggleClass('disabled', isDisplayingNonDocumentPlace);
            dashletToolbar.$(buttonSelector).attr('data-original-title', tooltipText);
        }, this);
    },

    /**
     * Initializes the popovers
     */
    initPopovers: function() {
        const fileForm = app.template.getView('cloud-drive', 'upload-form');
        const createFolderForm = app.template.getView('cloud-drive', 'create-folder');

        const dashlet = this.$el.closest('.dashlet');

        if (!this.displayingSites && !this.displayingDocumentDrives) {
            const uploadFileElement = dashlet.find('.uploadFile[rel=popover]');

            if (_.isFunction(uploadFileElement.dispose)) {
                uploadFileElement.dispose();
            }
            uploadFileElement.popover({
                container: dashlet,
                html: true,
                title: app.lang.get('LBL_UPLOAD_FILE'),
                content: fileForm,
                placement: 'bottom',
                sanitize: false,
            });

            const newFolderElement = dashlet.find('.newFolder[rel=popover]');

            if (_.isFunction(newFolderElement.dispose)) {
                newFolderElement.dispose();
            }
            newFolderElement.popover({
                container: dashlet,
                html: true,
                title: app.lang.get('LBL_CREATE_FOLDER'),
                content: createFolderForm,
                placement: 'bottom',
                sanitize: false,
            });
        }

        dashlet.find('[rel=popover]').on('show.bs.popover', _.bind(this.checkForPermission, this));
        dashlet.find('[rel=popover]').on('shown.bs.popover', _.bind(this.addPopoverEvents, this));
        dashlet.find('[rel=popover]').on('hidden.bs.popover', _.bind(this.removePopoverEvents, this));
        dashlet.find('.list-view').on('scroll', _.bind(this.adjustDropdowns, this));
        dashlet.find('[data-toggle=tooltip]').tooltip();
    },

    /**
     * Checks for permission to create folder or upload file
     *
     * @param {Event} evt
     */
    checkForPermission: function(evt) {
        if (this.folderId === 'root' && this.sharedWithMe) {
            app.alert.show('drive-permission-warning', {
                level: 'info',
                messages: app.lang.get('LBL_PERMISSION_ERROR'),
                autoClose: true,
            });

            return false;
        }
    },

    /**
     * Adjusts the dropdowns for better visibility
     *
     * @param {Event} evt
     */
    adjustDropdowns: function(evt) {
        if (this.disposed === true) {
            return;
        }

        const dropdowns = this.$('.btn-group.fieldset');
        const dashletBottom = this.$el.offset().top + this.$el.height();

        _.each(dropdowns, _.bind(function(dropdown) {
            if (this.isVisibleElement(dropdown)) {
                const offset = 2;
                const totalDropdownHeight = this.$(dropdown).innerHeight() +
                    this.$(dropdown).find('ul').height() +
                    offset;
                const dropdownOffset = this.$(dropdown).offset().top;
                const difference = dashletBottom - dropdownOffset;

                if (difference < totalDropdownHeight) {
                    $(dropdown).addClass('dropup');
                } else {
                    $(dropdown).removeClass('dropup');
                }
            }
        }, this));
    },

    /**
     * Checks if the element is visible
     *
     * @param {Element} element
     */
    isVisibleElement: function(element) {
        const rect = element.getBoundingClientRect();
        const top = rect.top;
        const bottom = rect.bottom;

        return top < window.innerHeight && bottom >= 0;
    },

    /**
     * Sorts columns
     *
     * @param {Event} evt
     */
    sortColumn: function(evt) {
        const target = this.$(evt.currentTarget);
        if (target.find('.sortable-row-header-container').length == 0) {
            return;
        }

        this.updateSortingStatus(target);
        const fieldName = target.data('fieldname');
        const direction = target.data('orderby');
        this.sortOptions = {
            direction: direction,
            fieldName: fieldName
        };

        this.loadFiles(null, true);
    },

    /**
     * Updates the sorting status for a column
     *
     * @param {jQuery} target
     */
    updateSortingStatus: function(target) {
        const status = target.data('orderby');
        const newStatus = status == 'asc' ? 'desc' : 'asc';
        target.data('orderby', newStatus);
    },

    /**
     * Triggers the showing of the tooltip
     *
     * @param {Event} evt
     */
    showTooltip: function(evt) {
        this.$(evt.currentTarget.firstElementChild).tooltip('show');
    },

    /**
     * Hides the tooltip
     *
     * @param {Event} evt
     */
    hideTooltip: function(evt) {
        this.$(evt.currentTarget.firstElementChild).tooltip('hide');
    },

    /**
     * Destoys the generated tooltips
     */
    destroyTooltips: function() {
        const tooltips = this.$('[data-toggle=tooltip]');
        tooltips.each(_.bind(function(index, tooltip) {
            $(tooltip).tooltip('dispose');
        }, this));
    },

    /**
     * Disposes the dropdowns
     */
    disposeDropdowns: function() {
        this.$('.list-view').off();
    },

    /**
     * Disposes the popovers
     */
    disposePopovers: function() {
        this.$('[rel=popover]').off();
    },

    /** Triggers the dashlet refresh
     *
     * @param {Event} evt
     */
    refreshClicked: function(evt) {
        evt.preventDefault();
        this.getRootFolder();
    },

    /** Update the cache associated with this dashlet
     *
     * @param {Object} data
     */
    _updateDashletCache: function(data) {
        let cache = app.cache.get(this.cid);
        _.extend(cache, data);
        app.cache.set(this.cid, cache);
    },

    /**
     * Adjusts the dashlet title and buttons when resizing
     */
    adjustHeaderPaneTitle: function() {
        const dashletToolbar = this.layout.getComponent('dashlet-toolbar');

        if (_.isEmpty(dashletToolbar)) {
            return;
        }

        const buttonsGroup = this.$('.btn-group.cd-radio-buttons button');

        if (buttonsGroup.length === 0) {
            return;
        }

        dashletTitle = dashletToolbar.$('.dashlet-title');
        const textWidth = this.getTextWidth(dashletTitle.text(), dashletTitle.css('font'));
        const titleRect = dashletTitle[0].getBoundingClientRect();
        const buttonGroupRect = buttonsGroup[0].getBoundingClientRect();
        this.titleLeft = titleRect.left === 0 ? this.titleLeft : titleRect.left;
        const buttonGroupLeft = buttonGroupRect.left;

        if ((this.titleLeft + textWidth) > buttonGroupLeft) {
            dashletTitle.hide();
            dashletToolbar.$el.addClass('pull-right');
        } else {
            dashletTitle.show();
            dashletToolbar.$el.removeClass('pull-right');
        }
    },

    /**
     * Copies the link to the document
     *
     * @param {Event} evt
     */
    copyLink: function(evt) {
        if (this.options.driveType !== 'dropbox') {
            return;
        }

        if (this.options.driveType === 'dropbox' && this.sharedWithMe && this.pathFolders.length === 1) {
            this.showClipboardConfirmation(evt.target.dataset.clipboardText);
            return;
        }

        const folderName = evt.target.dataset.name;
        const folderPath = this.getFolderPath(folderName);

        const url = app.api.buildURL('CloudDrive/shared', 'link');
        app.api.call('create', url, {
            'type': this.options.driveType,
            'folderPath': folderPath,
        }, {
            success: (result) => {
                this.showClipboardConfirmation(result.url);
            },
            error: _.bind(this._handleDriveError, this),
        });
    },

    /**
     * Displays an confirmation alert and calls setWebLink function
     *
     * @param string url
     */
    showClipboardConfirmation: function(url) {
        if (this.disposed) {
            return;
        }

        app.alert.show('copy_link', {
            level: 'confirmation',
            confirm: {
                label: app.lang.get('LBL_COPY_CLIPBOARD_CONFIRMATION')
            },
            title: app.lang.get('LBL_ALERT_TITLE_NOTICE'),
            templateOptions: {
                alertClass: 'alert-info',
                alertIcon: 'sicon-info-lg',
                indicatorClass: 'copy-clipboard-info'
            },
            messages: this.getMessages(url),
            autoClose: false,
            onConfirm: () => {
                this.setWebLink(url);
            },
        });
    },

    /**
     * Gets the messages for the copy to clipboard confirmation alert
     *
     * @param string url
     * @return string
     */
    getMessages: function(url) {
        return [
            app.lang.get('LBL_COPY_LINK_CONFIRMATION'),
            '<input readonly style="width: 90%; margin-top: 10px; background-color:transparent;" ' +
            'type="text" value="' + _.escape(url) + '">'
        ].join('<br>');
    },

    /**
     * Sets the web link on the clipboard
     *
     * @param string url
     */
    setWebLink: function(url) {
        if (navigator.clipboard && window.isSecureContext) {
            this._copyToNavigatorClipboard(url);
        } else {
            this._copyToDocumentClipboard(url);
        }
    },

    /**
     * Create alert
     *
     * @param {string} id
     * @param {string} level
     * @param {string} message
     */
    _createAlert: function(id, level, message) {
        app.alert.show(id, {
            level: level,
            messages: message,
        });
    },

    /**
     * Copy to navigator clipboard
     *
     * @param {string} url
     */
    _copyToNavigatorClipboard: function(url) {
        navigator.clipboard.writeText(url).then(() => {
            this._createAlert('copy-success', 'success', app.lang.get('LBL_TEXT_COPIED_TO_CLIPBOARD_SUCCESS'));
        });
    },

    /**
     * Copy to document clipboard
     *
     * @param {string} url
     */
    _copyToDocumentClipboard: function(url) {
        let textarea = document.createElement('textarea');
        textarea.textContent = url;
        textarea.style.position = 'fixed';

        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            this._createAlert('copy-success', 'success',app.lang.get('LBL_TEXT_COPIED_TO_CLIPBOARD_SUCCESS'));
        }
        catch (ex) {
            this._createAlert('copy-error', 'error',app.lang.get('LBL_TEXT_COPIED_TO_CLIPBOARD_ERROR'));
        }
        finally {
            document.body.removeChild(textarea);
        }
    },

    /**
     * Click on the toolbar button to create a new envelope in the current directory
     *
     * @param evt {Object}
     */
    newSignedDocument: function(evt) {
        if ($(evt.currentTarget).hasClass('disabled')) {
            return;
        }

        var drawerOptions = {
            layout: 'selection-list',
            context: {
                module: 'Documents',
                collection: app.data.createBeanCollection('Documents'),
                model: app.data.createBean('Documents')
            }
        };

        app.drawer.open(drawerOptions, this.sendDocumentToDocuSign.bind(this));
    },

    /**
     * Click on a document row action to send it to DocuSign
     *
     * @param {Event}
     */
    downloadDocumentInSugar: function(e) {
        this.listenToOnce(this, 'sugar-document:created', this.sendDocumentToDocuSign, this);

        this.createSugarDocument(e);
    },

    /**
     * Send document to DocuSign
     *
     * @param {Object} document
     */
    sendDocumentToDocuSign: function(document) {
        if (_.isUndefined(document)) {
            return;
        }
        if (_.isUndefined(document.id)) {
            return;
        }

        const documentId = document.id;
        const ctxModel = app.controller.context.get('model');
        const module = ctxModel.get('_module');
        const modelId = ctxModel.get('id');

        const documents = [documentId];
        let pathParam = this.folderId;
        if (this.options.driveType === 'dropbox') {
            pathParam = JSON.stringify(this.pathFolders);
        }

        app.events.trigger('docusign:send:initiate', {
            returnUrlParams: {
                parentRecord: module,
                parentId: modelId,
                token: app.api.getOAuthToken(),
                driveId: this.driveId,
            },
            documents: documents,
            cloudServiceName: this.options.driveType,
            cloudPath: pathParam
        });

        this.listenToOnce(app.events, 'docusign:send:finished', function() {
            if (this.disposed) {
                return;
            }

            if (this.options.driveType === 'sharepoint') {
                app.alert.show('send-docusign', {
                    level: 'info',
                    messages: app.lang.get('LBL_DOCUSIGN_PERMISSIONS'),
                });
            }
        });
    },

    /*
     * Merge document to Word/Excel/Powerpoint/Pdf
     *
     * @param {Event} e
     */
    mergeFile: function(e) {
        if ($(e.currentTarget).hasClass('disabled')) {
            return;
        }

        if (e.currentTarget.dataset.dashletaction === 'mergeWEP') {
            this.context.trigger('button:merge_template:click', app.controller.context.get('model'));
        } else {
            this.context.trigger('button:merge_template_pdf:click', app.controller.context.get('model'));
        }

        this.stopListening(app.events, 'docmerge:document:generated');
        this.listenToOnce(app.events, 'docmerge:document:generated', this.uploadDocument, this);
    },

    /**
     * Upload document to cloud
     *
     * @param {Object} document
     */
    uploadDocument: function(document) {
        const documentId = document.id;
        let formData = new FormData();
        formData.append('documentId', documentId);
        formData.append('cloud_service_type', this.options.driveType);
        let path = this.folderId;
        if (this.options.driveType === 'dropbox') {
            path = JSON.stringify(this.pathFolders);
        }
        formData.append('path', path);
        formData.append('driveId', this.driveId);

        const url = app.api.buildURL('CloudDrive', 'document');

        app.api.call('create', url, formData, {
            success: function(result) {
                app.alert.dismiss('merge_success');

                app.alert.show('upload-success', {
                    level: 'success',
                    messages: app.lang.get('LBL_UPLOAD_AND_LINK_COMPLETE', null, {documentName: result.documentName}),
                });
            },
            error: (error) => {
                this._handleDriveError(error, 'uploadFile');
            }
        }, {
            contentType: false,
            processData: false
        });
    },

    /**
     * Document Merge then Send to DocuSign
     *
     * @param {Event} evt
     */
    docMergeAndSendToDocuSign: function(evt) {
        if ($(evt.currentTarget).hasClass('disabled')) {
            return;
        }

        this.context.trigger('button:merge_template:click', app.controller.context.get('model'));

        this.stopListening(app.events, 'docmerge:document:generated');
        this.listenToOnce(app.events, 'docmerge:document:generated', this.sendDocumentToDocuSign, this);
    },

    /**
     * Gets the width of the title
     *
     * @param {string} text
     * @param {string} font
     */
    getTextWidth: function(text, font) {
        this.canvas = this.canvas ? this.canvas : document.createElement('canvas');
        const context = this.canvas.getContext('2d');
        context.font = font;
        const metrics = context.measureText(text);

        return metrics.width;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        app.cache.set(this.cid, null);
        app.events.off('cloud-drive:reload');
        $(window).off('resize.' + this.cid);
        this.hidePopover();
        this.disposePopovers();
        this.disposeDropdowns();
        this.removePopoverEvents();
        this.destroyTooltips();
        this._super('_dispose');
    }
})
