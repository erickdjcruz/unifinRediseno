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
 * @class View.Views.Base.AdministrationDrivePathRecordsView
 * @alias SUGAR.App.view.views.BaseAdminstrationDrivePathRecordsView
 * @extends View.Views.Base.View
 */
({
    /**
     * drive types that do support variable paths
     */
    variablePathDisabled: ['onedrive', 'sharepoint',],

    variablePathEnabled: ['google', 'dropbox',],

    /**
     * field types to use in paths
     */
    acceptedFieldTypes: [
        'varchar', 'text', 'datetime', 'relate', 'phone', 'url',
    ],

    /**
     * list of modules which can't be used
     */
    denyModules: [
        'Login', 'Home', 'WebLogicHooks', 'UpgradeWizard',
        'Styleguide', 'Activities', 'Administration', 'Audit',
        'Calendar', 'MergeRecords', 'Quotas', 'Teams', 'TeamNotices', 'TimePeriods', 'Schedulers', 'Campaigns',
        'CampaignLog', 'CampaignTrackers', 'Documents', 'DocumentRevisions', 'Connectors', 'ReportMaker',
        'DataSets', 'CustomQueries', 'WorkFlow', 'EAPM', 'Users', 'ACLRoles', 'InboundEmail', 'Releases',
        'EmailMarketing', 'EmailTemplates', 'SNIP', 'SavedSearch', 'Trackers', 'TrackerPerfs', 'TrackerSessions',
        'TrackerQueries', 'SugarFavorites', 'OAuthKeys', 'OAuthTokens', 'EmailAddresses',
        'Sugar_Favorites', 'VisualPipeline', 'ConsoleConfiguration', 'SugarLive',
        'iFrames', 'Sync', 'DataArchiver', 'MobileDevices',
        'PushNotifications', 'PdfManager', 'Dashboards', 'Expressions', 'DataSet_Attribute',
        'EmailParticipants', 'Library', 'Words', 'EmbeddedFiles', 'DataPrivacy', 'CustomFields', 'ArchiveRuns',
        'KBDocuments', 'KBArticles', 'FAQ', 'Subscriptions', 'ForecastManagerWorksheets', 'ForecastWorksheets',
        'pmse_Business_Rules', 'pmse_Project', 'pmse_Inbox', 'pmse_Emails_Templates'
    ],

    /**
     * initial record path
     */
    recordPath: '',

    /**
     * @inheritdoc
     */
    events: {
        'click .addField': 'addField',
        'change .moduleList': 'updateFieldList',
        'click .selectPath': 'selectPath',
        'click .savePath': 'savePath',
        'click .removePath': 'removePath',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this.driveType = this.context.get('driveType');

        this.getModuleList();
        this.loadPaths();
    },

    /**
     * initial load of saved paths
     */
    loadPaths: function() {
        app.alert.dismissAll();

        const url = app.api.buildURL('CloudDrivePaths', null, null, {
            max_num: -1,
            filter: [
                {
                    type: {
                        $equals: this.driveType
                    },
                    is_root: {
                        $equals: 0
                    },
                }
            ]
        });

        app.alert.show('path-loading', {
            level: 'process'
        });

        app.api.call('read', url, null, {
            success: _.bind(this._renderPaths, this),
            error: function(error) {
                app.alert.show('path-load-error', {
                    level: 'error',
                    messages: app.lang.get('LBL_DRIVE_LOAD_PATH_ERROR'),
                });
            },
        });
    },

    /**
     * Manipulate paths so we can render them
     *
     * @param {Array} data
     */
    _renderPaths: function(data) {
        app.alert.dismiss('path-loading');

        this.paths = _.isArray(data.records) && !_.isEmpty(data.records) ? data.records : [];

        for (let path of this.paths) {
            try {
                let pathDisplay = _.map(JSON.parse(path.path), function(item) {
                    return item.name;
                }).join('/');
                path.pathDisplay = pathDisplay;
            } catch (e) {
                path.pathDisplay = path.path;
            }
        }

        /**
         * Make sure we have one empty path at the begining
         */
        let rootName = app.lang.getAppString('LBL_MY_FILES');
        if (this.driveType !== 'sharepoint') {
            this.paths.unshift({
                path: '',
                pathDisplay: rootName,
            });
        } else {
            this.paths.unshift({
                path: '',
                pathDisplay: app.lang.get('LBL_DEFAULT_STARTING_PATH', this.module),
            });
        }
        this.render();
    },

    /**
     * set initial record path upon addition
     *
     * @param {string} module
     * @param {Event} evt
     * @param {string} path
     */
    setRecordPath: function(module, evt, path) {
        let defaultRecordName = module === 'Contacts' || module === 'Leads' ?
            `${module}/$first_name $last_name` : `${module}/$name`;

        if (!module) {
            defaultRecordName = '';
        }

        this.$(evt.target)
            .parent()
            .parent()
            .children('.span3')
            .children('.recordPath')
            .val(defaultRecordName);
    },

    /**
     * @inheritdoc
     */
    _render: function(options) {
        this._super('_render', arguments);

        this.initDropdowns();
    },

    /**
     * list of available modules
     */
    getModuleList: function() {
        let modulesMeta = app.metadata.getModules({
            filter: 'display_tab',
            access: true,
        });

        this.modules = Object.keys(modulesMeta)
            .filter(key => !this.denyModules.includes(key))
            .reduce((obj, key) => {
                obj[key] = modulesMeta[key];
                return obj;
            }, {});
    },

    /**
     * dropdowns as select2
     */
    initDropdowns: function() {
        this.$('.moduleList').select2({
            autoClear: true,
            containerCssClass: 'select2-choices-pills-close',
            placeholder: app.lang.get('LBL_SELECT_MODULE', this.module),
        });

        this.$('.moduleList').trigger('change');
    },

    /**
     * Add a field variable to the record path
     *
     * @param {Event} evt
     */
    addField: function(evt) {
        let fieldDropdown = this.$(evt.target)
                                .closest('.span6')
                                .parent()
                                .children('.span6')
                                .children('.fieldList');
        let fieldName = fieldDropdown.select2('data').id;
        let recordPath = this.$(evt.target)
                             .closest('.span6')
                             .parent()
                             .children('.span3')
                             .children('.recordPath');
        let currentRecordPath = recordPath.val();
        let newPath = currentRecordPath.concat(fieldName);

        recordPath.val(newPath);
    },

    /**
     * Whenever the module changes we need to make sure the field list changes
     *
     * @param {Event} evt
     */
    updateFieldList: function(evt) {
        let _dropdown = this.$(evt.target)
                            .parent()
                            .parent()
                            .children('.span6')
                            .children('.fieldList');
        let path = this.$(evt.target)
                       .closest('.span3')
                       .parent()
                       .find('.recordPath')
                       .val();
        let _module = this.$(evt.target)
                          .parent()
                          .find('select.moduleList')
                          .val();
        let dropdownFields = [];
        if (_.isObject(this.modules[_module]) && _.has(this.modules[_module], 'fields')) {
            let fields = _.filter(this.modules[_module].fields, function(field) {
                return field.type !== 'link' &&
                    field.name &&
                    typeof field.name === 'string' &&
                    field.name.length > 0;
            });
            _.each(fields, function(field) {
                if (_.isObject(field)) {
                    let itemName = app.lang.get(field.vname, _module) || field.name;
                    let itemId = `$${field.name}`;
                    dropdownFields.push({
                        id: itemId,
                        text: itemName,
                    });
                }
            });
        }

        _dropdown.select2({
            data: {
                results: dropdownFields
            }
        });
    },

    /**
     * Opens the remote selection drawer so we can select paths from drive
     *
     * @param {Event} evt
     */
    selectPath: function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        const pathModule = this.$(evt.target)
            .parents('.row-fluid')
            .children('.span3')
            .children('select.moduleList').val();

        const pathId = evt.target.dataset.id;

        if (_.isEmpty(pathModule)) {
            app.alert.show('module-required', {
                level: 'error',
                messages: app.lang.getModString('LBL_MODULE_REQUIRED', this.module),
            });
            return;
        }

        // open the selection drawer
        app.drawer.open({
            context: {
                pathModule: pathModule,
                isRoot: false,
                parentId: 'root',
                folderName: '',
                driveType: this.driveType,
                pathId: pathId
            },
            layout: 'drive-path-select',
        }, _.bind(this.loadPaths, this));
    },

    /**
     * Save a path
     *
     * @param {Event} evt
     */
    savePath: function(evt) {
        const pathModule = this.$(evt.target)
            .parents('.row-fluid')
            .children('.span3')
            .children('select.moduleList').val();

        const pathId = evt.target.dataset.id;

        // we cannot save a module path without module
        if (!pathModule) {
            app.alert.show('module-required', {
                level: 'error',
                messages: app.lang.getModString('LBL_MODULE_REQUIRED', this.module),
            });
            return;
        }

        const path = this.$(evt.target)
            .parents('.row-fluid')
            .children('.span3')
            .children('.recordPath').val() || 'My files';

        const url = app.api.buildURL('CloudDrive', 'path');

        app.alert.show('path-saving-processing', {
            level: 'process'
        });

        const pathRow = this.$(evt.target).parents('.row-fluid.path');
        const isShared = pathRow.data('isshared');
        let folderId = pathRow.data('folderid');
        const currentPath = pathRow.data('currentpath');
        const driveId = pathRow.data('driveid');

        //reset folder id if paths do not match
        if (currentPath !== path) {
            folderId = null;
        }

        app.api.call('create', url, {
            pathModule: pathModule,
            isRoot: false,
            type: this.driveType,
            drivePath: path,
            isShared: isShared,
            folderId: folderId,
            driveId: driveId,
            pathId: pathId
        } , {
            success: _.bind(function() {
                app.alert.show('path-saved', {
                    level: 'success',
                    messages: app.lang.getModString('LBL_PATH_SAVED', this.module),
                });
                this.loadPaths();
            }, this),
            error: function(error) {
                app.alert.show('path-error', {
                    level: 'error',
                    messages: error.message,
                });
            },
            complete: function() {
                app.alert.dismiss('path-saving-processing');
            }
        });
    },

    /**
     * Remove a path
     *
     * @param {Event} evt
     */
    removePath: function(evt) {
        const pathId = evt.target.dataset.id;
        const url = app.api.buildURL('CloudDrive', 'path');

        app.api.call('delete', url, {
            pathId: pathId,
        }, {
            success: _.bind(function() {
                app.alert.show('path-deleted', {
                    level: 'success',
                    messages: app.lang.get('LBL_ROOT_PATH_REMOVED', this.module),
                });
                this.loadPaths();
            }, this),
        });
    }
});
