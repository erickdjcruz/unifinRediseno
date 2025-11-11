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
 * @class View.Fields.Base.DRIWorkflowTaskTemplates.BlockedByField
 * @alias SUGAR.App.view.fields.BaseDRIWorkflowTaskTemplatesBlockedByField
 * @extends View.Fields.Base.EnumField
 */
({
    /**
     * BlockedBy FieldTemplate (base)
     */
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'enum';
    },

    /**
     * @inheritdoc
     */
    loadEnumOptions: function(fetch, callback) {
        let request;
        let _key = `request:${this.module}:${this.name}`;

        if (fetch || !this.items) {
            this.isFetchingOptions = true;
            let workflowTemplateId = this.model.get('dri_workflow_template_id');
            if (
                _.isEmpty(workflowTemplateId) &&
                !_.isEmpty(this.context) && !_.isEmpty(this.context.parent) &&
                !_.isEmpty(this.context.parent.get('model'))
            ) {
                const parentModel = this.context.parent.get('model');
                workflowTemplateId = parentModel.get('dri_workflow_template_id');
            }
            if (this.context.get(_key)) {
                request = this.context.get(_key);
                request.xhr.done(
                    _.bind(function(o) {
                        callback.call(this);
                    }, this)
                );
            } else if (!_.isEmpty(workflowTemplateId)) {
                let link = '';
                let fieldName = this.name;
                if (_.isEqual(fieldName, 'blocked_by')) {
                    link = 'dri_workflow_task_templates';
                } else if (_.isEqual(fieldName, 'blocked_by_stages')) {
                    link = 'dri_subworkflow_templates';
                }
                if (!_.isEmpty(link)) {
                    request = app.api.relationships('read', 'DRI_Workflow_Templates',
                        {
                            id: workflowTemplateId,
                            link: link,
                        },
                        {
                            max_num: -1, // make sure to fetch all activities
                        },
                        {
                            success: _.bind(this._readRelationshipsSuccess, this, callback),
                        }
                    );
                }
                this.context.set(_key, request);
            }
        }
    },

    /**
     * Provide label of the field depending on field name
     *
     * @param {Bean} record
     * @return {string|undefined}
     */
    _getLabel: function(record) {
        let fieldName = this.name;
        if (_.isEqual(fieldName, 'blocked_by')) {
            return `${record.stage_template_label} - ${record.sort_order}. ${record.name}`;
        } else if (_.isEqual(fieldName, 'blocked_by_stages')) {
            return record.label;
        }
    },

    /**
     * Read relationships success handler
     *
     * @param {callback} callback
     * @param {Object} response
     * @return {undefined}
     */
    _readRelationshipsSuccess: function(callback, response) {
        if (this.disposed) {
            return;
        }
        this.items = {};
        let fieldName = this.name;
        let _key = `request:${this.module}:${this.name}`;

        let records = _.sortBy(response.records, function(record) {
            return this._getLabel(record);
        }, this);

        if (_.isEqual(fieldName, 'blocked_by')) {
            let blockedByCheck;
            _.each(records, function(record) {
                blockedByCheck = false;
                if (this.model.get('is_parent') === true && record.is_parent === false &&
                    _.isEqual(record.parent_id, this.model.id)) {
                    // parent should not blocked by its child
                    blockedByCheck = true;
                } else if (this.model.get('is_parent') === false && record.is_parent === true &&
                    _.isEqual(record.id, this.model.get('parent_id'))) {
                    // child should not blocked by its parent
                    blockedByCheck = true;
                }
                if (!blockedByCheck && this.model.id !== record.id) {
                    this.items[record.id] = this._getLabel(record);
                }
            }, this);
        } else if (_.isEqual(fieldName, 'blocked_by_stages')) {
            let currentActivity = app.data.createBean(this.module, {
                id: this.model.id,
            });
            currentActivity.fetch({
                success: _.bind(this._beanFetchSuccess , this, records, callback),
            });
        }
        this.context.unset(_key);
        callback.call(this);
    },

    /**
     * Bean fetch success handler
     *
     * @param {Array} records
     * @param {callback} callback
     * @param {Bean} currentActivity
     * @return {undefined}
     */
    _beanFetchSuccess: function(records, callback, currentActivity) {
        let _key = `request:${this.module}:${this.name}`;
        let stageId = currentActivity.attributes.dri_subworkflow_template_id;
        if (this.model) {
            if (_.isEmpty(this.model.get('id')) && !_.isEmpty(this.model.get('dri_subworkflow_template_id'))) {
                stageId = this.model.get('dri_subworkflow_template_id');
            }
            _.each(records, function(record) {
                if (stageId !== record.id) {
                    this.items[record.id] = this._getLabel(record);
                }
            }, this);
            this.context.unset(_key);
        }
        callback.call(this);
    },
});
