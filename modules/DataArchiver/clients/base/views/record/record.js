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
 * @class View.Views.Base.DataArchiver.RecordView
 * @alias SUGAR.App.view.views.BaseDataArchiverRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    moduleRequirements: {
        'pmse_Inbox': [
            'cas_status',
        ],

    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.events = _.extend({}, this.events, {
            'click [name="perform_button"]': 'performClicked',
        });
    },

    /**
     * Function that defines behavior when the Archive Now button is clicked on the record view
     */
    performClicked: function() {
        if (this.disposed) {
            return;
        }
        var self = this;
        var url = app.api.buildURL('DataArchiver/' + this.model.id + '/run', null, null, null);
        var data = this.model.attributes;
        app.alert.dismissAll();
        app.alert.show('delete_warning', {
            level: 'confirmation',
            title: app.lang.get('LBL_ARCHIVER_WARNING_TITLE', 'DataArchiver'),
            messages: app.lang.get('LBL_ARCHIVER_WARNING', 'DataArchiver'),
            autoclose: false,
            onConfirm: function() {
                self.disposed = true;
                app.api.call('create', url, {}, {
                    success: function(results) {
                        app.alert.show('success', {
                            level: 'success',
                            autoClose: true,
                            autoCloseDelay: 10000,
                            title: app.lang.get('LBL_ARCHIVE_SUCCESS_TITLE', 'DataArchiver') + ':',
                            messages: data.process_type === 'archive' ?
                                app.lang.get('LBL_ARCHIVE_SUCCESS', 'DataArchiver') :
                                app.lang.get('LBL_DELETE_SUCCESS', 'DataArchiver')
                        });
                        self.layout.trigger('subpanel_refresh');
                    },
                    error: function(e) {
                        if (e.code === 'ModuleReqError') {
                            self.showModuleRequirementsError(e.message, self.model.get('filter_module_name'));
                        } else {
                            app.alert.show('error', {
                                level: 'error',
                                title: app.lang.get('LBL_ARCHIVE_ERROR', 'DataArchiver') + ':',
                                messages: ['ERR_HTTP_500_TEXT_LINE1', 'ERR_HTTP_500_TEXT_LINE2']
                            });
                        }
                    },
                    complete: function() {
                        self.disposed = false;
                    }
                });
            }
        });
    },

    /**
     * @override
     */
    saveClicked: function() {
        let canSave = true;
        const filterModule = this.model.get('filter_module_name');
        if (this.model.get('filter_def') && filterModule in this.moduleRequirements) {
            const filters = JSON.parse(this.model.get('filter_def')).map(f => Object.keys(f)[0]);
            const reqsNotMet = this.moduleRequirements[filterModule].filter(f => !filters.includes(f));
            if (reqsNotMet.length > 0) {
                // If there are many reqs not met, just show the first in error me
                this.showModuleRequirementsError(reqsNotMet[0], filterModule);
                canSave = false;
            }
        }

        canSave && this._super('saveClicked');
    },

    /**
     * Show the error message that will occur when module requirements are not met. Will only show the first of any
     * requirements not met
     */
    showModuleRequirementsError(field, module) {
        app.alert.dismissAll();
        const fieldName = app.lang.get(app.metadata.getModule(module).fields[field].vname, module) || field;
        const args = {fieldName: fieldName, moduleName: module};
        app.alert.show('req_not_met', {
            level: 'error',
            messages: app.lang.get('TPL_PMSE_INBOX_ERROR_MESSAGE', this.module, args),
            autoClose: true,
            autoCloseDelay: 5000
        });
    }
})
