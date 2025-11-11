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
 * @class View.Views.Base.AdministrationDenormFrameworkView
 * @alias SUGAR.App.view.views.BaseDenormFrameworkView
 * @extends View.Views.Base.ConfigPanelView
 */
({
    extendsFrom: 'ConfigPanelView',

    denormFieldList: null,
    moduleFields: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        var self = this;

        this.model.on('change:modules', function() {
            self.updateFieldList();
        });

        this.context.on('relate-denormalization:save', _.bind(this.save, this));

        this.context.on('field-lists:pending', function() {
            self.layout.getComponent('config-header-buttons').enableButton(true);
        });

        this.context.on('field-lists:initial', function() {
            self.layout.getComponent('config-header-buttons').enableButton(false);
        });
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');
        var module = app.cache.get('relate-denormalization.selected-module');
        if (module) {
            this.model.set('modules', module);
        }
    },

    updateFieldList: function() {
        var moduleList = this.getField('modules');
        if (!moduleList || moduleList.waitingForRender) {
            return;
        }
        var module = moduleList.getFormattedValue();
        app.cache.set('relate-denormalization.selected-module', module);
        var fields = this.getFieldsForModule(module);
        this.getField('field-lists').refresh(fields, this.getField('modules').getFieldsForModule(module));
        this.layout.getComponent('config-header-buttons').enableButton(false);
    },

    getFieldsForModule: function(module) {
        if (this.moduleFields === null || this.moduleFields[module] === undefined) {
            var moduleDef = App.metadata.getModule(module);

            if (!moduleDef) {
                return [];
            }

            var moduleFields = [];
            var listView = moduleDef.views ? moduleDef.views.list : null;
            if (listView && listView.meta && listView.meta.panels && listView.meta.panels[0] &&
                listView.meta.panels[0].fields) {
                moduleFields = listView.meta.panels[0].fields;
            }

            var fieldDefs = moduleDef.fields || [];

            moduleFields = _.map(moduleFields, function(field) {
                let isSortable = field.sortable !== false;
                field = fieldDefs[field.original_name || field.name] || field;
                field.sortable = isSortable;

                return field;
            });

            moduleFields = _.filter(moduleFields, function(field) {
                // Users.full_name field ignored due to complicated structure (last_name + first_name)
                let isUserFullName = field.rname === 'full_name' && field.module === 'Users';
                let isSortable = field.sortable !== false;

                return field.type === 'relate' && !isUserFullName && isSortable;
            });

            this.moduleFields = this.moduleFields || {};
            this.moduleFields[module] = moduleFields;
        }

        return this.moduleFields[module] || [];
    },

    save: function() {
        self = this;
        app.api.call(
            'create',
            app.api.buildURL(this.module, 'denormalization/pre-check'),
            this.model.toJSON(),
            {
                success: _.bind(function(data) {
                    if (data && data.message) {
                        app.alert.show('relate-denormalization-warning', {
                            level: 'error',
                            title: app.lang.get('LBL_ERROR'),
                            messages: data.message,
                        });
                    } else if (data && data.report) {
                        data = self.preparePreCheckResponse(data);

                        var template = app.template.getView('relate-denormalization.pre-check-result', this.module);
                        data.module = this.module;

                        app.alert.show('relate-denormalization-warning', {
                            level: data.message_type,
                            title: app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_PRE_CHECK_RESULTS', this.module),
                            messages: template(data),
                            onConfirm: _.bind(function() {
                                this.runProcess(data);
                            }, this)
                        });
                    }
                }, this),
                error: function() {
                    app.alert.show('relate-denormalization-warning', {
                        level: 'error',
                        title: app.lang.get('LBL_ERROR'),
                    });
                }
            }
        );
    },

    runProcess: function(preCheckData) {
        self = this;

        var template = app.template.getView('relate-denormalization.start-process', this.module);
        var modal = app.alert.show('start-process', {
            title: app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_PROCESS_RUNNING', this.module) + ' ' +
                (
                    preCheckData.report[0].is_denormalized ?
                        app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_NORMALIZATION', this.module) :
                        app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_DENORMALIZATION', this.module)
                ),
            messages: template(preCheckData)
        });

        self.layout.getComponent('config-header-buttons').enableButton(false);
        var metadataSyncRequired = true;

        app.api.call(
            'create',
            app.api.buildURL(this.module, 'denormalization/apply'),
            this.model.toJSON(),
            {
                success: function(data) {
                    if (!data || !data.ok) {
                        app.alert.show('relate-denormalization-warning', {
                            level: 'error',
                            title: app.lang.get('LBL_ERROR'),
                            messages: data.message || 'Error'
                        });
                    } else if (data.denormalized) {
                        metadataSyncRequired = false;
                    }
                },
                error: function() {
                    app.alert.show('relate-denormalization-warning', {
                        level: 'error',
                        title: app.lang.get('LBL_ERROR'),
                        messages: app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_COMMUNICATION_ERROR', self.module)
                    });
                },
                complete: function() {
                    if (metadataSyncRequired) {
                        app.metadata.sync(function() {
                            self.getField('job-list').loadData();
                            modal.close();

                            app.alert.show('relate-denormalization-success', {
                                level: 'info',
                                title: 'success',
                                messages: app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_SYNC_COMPLETE', self.module),
                                autoClose: true
                            });

                            self.getField('modules').load();
                        });
                    } else {
                        app.alert.show('relate-denormalization-success', {
                            level: 'info',
                            title: 'success',
                            messages: app.lang.get('LBL_MANAGE_RELATE_DENORMALIZATION_SYNC_COMPLETE', self.module),
                            autoClose: true
                        });
                        self.getField('job-list').loadData();
                        self.getField('modules').load();
                        modal.close();
                    }
                }
            },
            {cache: false}
        );
    },

    preparePreCheckResponse: function(data) {
        data.message_type = 'error';
        if (data.overall_possibility) {
            data.message_type = 'confirmation';
        }

        _.each(data.report, function(item) {
            if (item.details && item.details.count_rhs) {
                var det = item.details;
                det.estimation_unit = 'seconds';

                if (det.estimated_time > 180) {
                    det.estimation_unit = 'minutes';
                    det.estimated_time = Math.round(det.estimated_time / 60);
                }
                if (det.estimated_time > 180) {
                    det.estimation_unit = 'hours';
                    det.estimated_time = Math.round(det.estimated_time / 60);
                }

                det.count_rhs = det.count_rhs.toLocaleString();
                det.count_rel = det.count_rel.toLocaleString();
                det.count_lhs = det.count_lhs.toLocaleString();
            }
        });

        return data;
    }
})
