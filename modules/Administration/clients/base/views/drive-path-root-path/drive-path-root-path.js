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
 * @class View.Views.Base.AdministrationDrivePathRootPathView
 * @alias SUGAR.App.view.views.BaseAdminstrationDrivePathRootPathView
 * @extends View.Views.Base.View
 */
({
    /**
     * Initial root id
     */
    rootId: 'root',

    /**
     * @inheritdoc
     */
    events: {
        'click .selectRootPath': 'selectRootPath',
        'click .removeRootPath': 'removeRootPath',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this.driveType = this.context.get('driveType');
        this.driveTypeLabel = app.lang.getAppListStrings('drive_types')[this.driveType];

        /**
         * Load from the database the root path
         */
        this.loadRootPath();
    },

    /**
     * Root path loading
     */
    loadRootPath: function() {
        app.alert.dismissAll();

        const url = app.api.buildURL('CloudDrivePaths', null, null, {
            max_num: -1,
            filter: [
                {
                    type: {
                        $equals: this.driveType
                    },
                    is_root: {
                        $equals: 1
                    }
                }
            ]
        });

        app.alert.show('path-loading', {
            level: 'process'
        });

        app.api.call('read', url, null, {
            success: _.bind(this._renderRootPath, this),
            error: function(error) {
                app.alert.show('drive-error', {
                    level: 'error',
                    messages: error.message,
                });
            },
        });
    },

    /**
     * Render root path
     *
     * @param {Array} data
     */
    _renderRootPath: function(data) {
        app.alert.dismiss('path-loading');
        this.rootPath = _.isArray(data.records) && data.records[0] ? data.records[0] : {path: ''};

        try {
            if (!_.isUndefined(this.rootPath)) {
                this.rootPathDisplay = _.map(JSON.parse(this.rootPath.path), function(item) {
                    return item.name;
                }).join('/');
            }
        } catch (err) {
            this.rootPathDisplay = this.driveType === 'sharepoint' ?
                app.lang.get('LBL_SHAREPOINT_ROOT_PATH', this.module) :
                '';
        }

        this.render();
    },

    /**
     * Opent the path selection drawer
     *
     * @param {Event} evt
     */
    selectRootPath: function(evt) {
        evt.preventDefault();
        evt.stopPropagation();

        // open the selection drawer
        app.drawer.open({
            context: {
                pathModule: null,
                isRoot: true,
                parentId: 'root',
                driveType: this.driveType,
            },
            layout: 'drive-path-select',
        }, _.bind(function() {
            this.loadRootPath();
        }, this));
    },

    /**
     * Removes the root path
     *
     * @param {Event} evt
     */
    removeRootPath: function(evt) {
        const url = app.api.buildURL('CloudDrive', 'path');

        app.api.call('delete', url, {
            pathId: this.rootPath.id,
        }, {
            success: _.bind(function() {
                app.alert.show('path-deleted', {
                    level: 'success',
                    messages: app.lang.get('LBL_ROOT_PATH_REMOVED', this.module),
                });
                this.loadRootPath();
            }, this),
            error: _.bind(function(error) {
                app.alert.show('path-delete-error', {
                    level: 'error',
                    messages: error.message,
                });
            }, this),
        });
    }
});
