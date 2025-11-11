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
(function register(app) {
    app.events.on('app:init', function init() {
        /**
         * Open URL Action
         *
         * @class Core.Actions.UpdateRecordAction
         * @alias SUGAR.App.Actions.UpdateRecordAction
         *
         * @param {Object} def Action Definition
         */
        function UpdateRecord(def) {
            this.def = def;
        }

        /**
         * Update record field values
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
        */
        UpdateRecord.prototype.run = function(opts, currentExecution) {
            const fieldDef = this.def;
            const properties = fieldDef.properties;
            const fieldsToBeUpdated = properties.fieldsToBeUpdated;

            if (!this.hasEditAccess(opts.recordModel, fieldsToBeUpdated)) {
                app.alert.show('alert_no_access', {
                    level: 'error',
                    title: app.lang.get('ERR_NO_VIEW_ACCESS_TITLE'),
                    messages: app.lang.get('ERR_NO_VIEW_ACCESS_ACTION'),
                    autoClose: true,
                    autoCloseDelay: 5000
                });

                currentExecution.nextAction();

                return;
            };

            let computedFields = {};
            let hardCodedFields = {};

            _.each(fieldsToBeUpdated, function update(fieldMeta, fieldKey) {
                const isCalculated = fieldMeta.isCalculated;

                if (isCalculated) {
                    computedFields[fieldKey] = fieldMeta;
                } else {
                    hardCodedFields[fieldKey] = fieldMeta;
                }
            });

            const totalComputedFields = Object.keys(computedFields).length;
            const totalHardCodedFields = Object.keys(hardCodedFields).length;

            if (totalHardCodedFields === 0 && totalComputedFields === 0) {
                currentExecution.nextAction();
            } else if (totalComputedFields === 0) {
                this.updateRecord(fieldsToBeUpdated, opts, currentExecution);
            } else {
                this.resolveComputedValues(fieldsToBeUpdated, computedFields, opts, currentExecution);
            }
        };

        /**
         * Check acl access for fields that are being updated
         *
         * @param {Data.Bean} model
         * @param {Object} fieldsToBeUpdated
         *
         * @return {boolean}
         */
        UpdateRecord.prototype.hasEditAccess = function(model, fieldsToBeUpdated) {
            let hasAccess = app.acl.hasAccessToModel('edit', model) &&
                _.chain(fieldsToBeUpdated)
                    .values()
                    .map(function getFieldName(field) {
                        return field.fieldName;
                    })
                    .reduce(function verifyFieldAccess(hasAccess, fieldName) {
                        return hasAccess && app.acl.hasAccessToModel('edit', model, fieldName);
                    }, true)
                    .value();

            return hasAccess;
        };

        /**
         * Evaluates calculated field values
         *
         * @param {Object} allFieldsMeta Object that will be populated with calculated values
         * @param {Object} computedFieldsMeta Information about what fields to update
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        UpdateRecord.prototype.resolveComputedValues = function(
            allFieldsMeta,
            computedFieldsMeta,
            opts,
            currentExecution
        ) {
            let recordModel = opts.recordModel;

            const requestType = 'create';
            const apiPath = 'actionButton/evaluateExpression';

            let requestMeta = {
                targetFields: computedFieldsMeta,
                targetRecordId: recordModel.id,
                targetModule: recordModel.module
            };

            const apiCallbacks = {
                success: _.bind(function fieldsCalculatedCallback(result) {
                    _.each(result, function assignNewValues(item, key) {
                        const value = {};

                        value[key] = item;

                        allFieldsMeta[key].value = value;
                    });

                    this.updateRecord(allFieldsMeta, opts, currentExecution);
                }, this)
            };

            const apiUrl = app.api.buildURL(apiPath, requestType, requestMeta, {});

            app.api.call(requestType, apiUrl, requestMeta, null, apiCallbacks);
        };

        /**
         * After we patch the record in updateRecord() we will need to refetch the current context model.
         * This function basically re-applies whatever changes the user might've done to the model
         * and has not synched to the server to the re-fetched model
         *
         * @param {Data.Bean} model Current bean model
         * @param {Object} updateFields A simple object with field values that were updated by action
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        UpdateRecord.prototype.fetchAndApplyChanges = function(model, updatedFields, currentExecution) {
            let changes = model.changedAttributes(model.getSynced());
            changes = !_.isEmpty(changes) ? _.pick(model.changedAttributes(), _.keys(changes)) : {};
            changes = _.omit(changes, _.keys(updatedFields));
            changes = _.omit(changes, ['date_modified']);

            model.fetch({
                complete: function reapplyChanges() {
                    model.set(changes);
                    currentExecution.nextAction();
                }
            });
        };

        /**
         * Process actionbutton fullname update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        UpdateRecord.prototype.normalizeFullameValue = function(fieldDef, fieldData) {
            if (_.isString(fieldData.value[fieldData.fieldName]) &&
                _.isUndefined(fieldData.value.first_name) &&
                _.isUndefined(fieldData.value.last_name)) {
                // Handling calculated value for full name, so we need to split it to a first_name/last_name
                // value pair and apply it to the record.
                let nameParts = fieldData.value[fieldData.fieldName].split(' ');
                let lastName = nameParts.pop().trim();
                let firstName = nameParts.join(' ').trim();

                return {
                    first_name: firstName,
                    last_name: lastName
                };
            } else {
                return fieldData.value;
            }
        };

        /**
         * Process actionbutton datetime update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        UpdateRecord.prototype.normalizeDatetimeValue = function(fieldDef, fieldData) {
            if (_.isString(fieldData.value[fieldData.fieldName])) {
                let value = {};

                const userDateTimeFormat = `${app.date.getUserDateFormat()} ${app.date.getUserTimeFormat()}`;
                const dateValue = fieldData.value[fieldData.fieldName];
                const date = app.date(dateValue, userDateTimeFormat);

                if (date.isValid()) {
                    value[fieldData.fieldName] = date.format();
                } else {
                    value[fieldData.fieldName] = app.date.utc(fieldData.value[fieldData.fieldName]).format();
                }

                return value;
            } else {
                return fieldData.value;
            }
        };

        /**
         * Process actionbutton relate update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        UpdateRecord.prototype.normalizeRelateValue = function(fieldDef, fieldData) {
            let idName = fieldDef.id_name;

            if (!_.isEmpty(fieldData.value[idName])) {
                return fieldData.value;
            } else {
                let value = {};

                value[fieldData.fieldName] = fieldData.value[fieldData.fieldName];
                value[idName] = fieldData.value[fieldData.fieldName];

                return value;
            }
        };

        /**
         * Process actionbutton parent update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        UpdateRecord.prototype.normalizeParentValue = function(fieldDef, fieldData) {
            let typeName = fieldDef.type_name;
            let idName = fieldDef.id_name;

            let value = {};

            if (_.isObject(fieldData.value.parent)) {
                // sourcing data from the trimmed down parent record object
                value[fieldData.fieldName] = fieldData.value.parent.name;
                value[typeName] = fieldData.value.parent._module;
                value[idName] = fieldData.value.parent.id;
            } else if (!_.isEmpty(fieldData.value[typeName]) && !_.isEmpty(fieldData.value[idName])) {
                // no trimmed down record, but we have the actual field data
                value[fieldData.fieldName] = fieldData.value[fieldData.fieldName];
                value[typeName] = fieldData.value[typeName];
                value[idName] = fieldData.value[idName];
            } else {
                // no actual field data, formula must have been used,
                // need to decode it from a bar separate format, eg:
                // <module>|<id>|<name>
                let parentParts = fieldData.value[fieldData.fieldName].split('|');
                let recordModule = (parentParts.shift() || '').trim();
                let recordId = (parentParts.shift() || '').trim();
                let recordName = (parentParts.join('') || '').trim();

                value[fieldData.fieldName] = recordName;
                value[typeName] = recordModule;
                value[idName] = recordId;
            }

            return value;
        };

        /**
         * Process actionbutton tag update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        UpdateRecord.prototype.normalizeTagValue = function(fieldDef, fieldData) {
            if (_.isString(fieldData.value[fieldData.fieldName])) {
                let value = {};

                value[fieldData.fieldName] = fieldData.value[fieldData.fieldName].split(',');

                return value;
            } else {
                return fieldData.value;
            }
        };

        /**
         * Certain field values need to be processed a bit before being applied to the model.
         *
         * @param {Object} fieldData
         * @param {Data.Bean} model
         * @return {Array}
         */
        UpdateRecord.prototype.normalizeFieldValue = function(fieldData, model) {
            let result = [];
            let fieldDef = model.fields[fieldData.fieldName];

            switch (fieldDef.type) {
                case 'fullname':
                    result.push(this.normalizeFullameValue(fieldDef, fieldData));
                    break;
                case 'datetime':
                case 'datetimecombo':
                    result.push(this.normalizeDatetimeValue(fieldDef, fieldData));
                    break;
                case 'relate':
                    result.push(this.normalizeRelateValue(fieldDef, fieldData));
                    break;
                case 'parent':
                    result.push(this.normalizeParentValue(fieldDef, fieldData));
                    break;
                case 'tag':
                    result.push(this.normalizeTagValue(fieldDef, fieldData));
                    break;
                default:
                    result.push(fieldData.value);
                    break;
            }

            return result;
        };

        /**
         * Sets evaluated values on bean
         *
         * @param {Object} fieldValues Calculated field values
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        UpdateRecord.prototype.updateRecord = function(fieldValues, opts, currentExecution) {
            let recordModel = opts.recordModel;

            fieldValues = _.chain(fieldValues)
                .values()
                .map(a => this.normalizeFieldValue(a, recordModel))
                .flatten(1)
                .map(a => _.pairs(a))
                .flatten(1)
                .object()
                .value();

            if (this.def.properties.autoSave) {
                let patchValues = _.assign({}, fieldValues, {
                    id: recordModel.get('id')
                });

                let patchModel = app.data.createBean(recordModel.module, patchValues);

                patchModel.save({}, {
                    showAlerts: true,
                    success: _.bind(function successSave() {
                        this.fetchAndApplyChanges(recordModel, fieldValues, currentExecution);
                    }, this),
                    error: _.bind(function errorSave() {
                        this.fetchAndApplyChanges(recordModel, fieldValues, currentExecution);
                    }, this)
                });
            } else {
                recordModel.set(fieldValues);

                if (opts.recordView && opts.recordView.name === 'dashablerecord') {
                    opts.recordView.editRecord();
                } else if (opts.recordView && opts.recordView.name === 'preview') {
                    let previewHeader = opts.recordView.layout.getComponent('preview-header');

                    if (previewHeader) {
                        previewHeader.triggerEdit();
                    }
                } else if (
                    opts.recordView &&
                    (opts.recordView.type === 'subpanel-list' || opts.recordView.type === 'recordlist')
                ) {
                    opts.recordView.editClicked(opts.recordModel, opts.buttonField);
                } else if (opts.recordView && opts.recordView.name === 'dashlet-toolbar') {
                    opts.recordView.layout.getComponent('dashablerecord').editRecord();
                } else {
                    if (opts.recordView && opts.recordView.context) {
                        opts.recordView.context.trigger('button:edit_button:click');
                    }

                    app.controller.context.trigger('button:edit_button:click');
                }

                currentExecution.nextAction();
            }
        };

        app.actions = app.actions || {};

        app.actions = _.extend(app.actions, {
            UpdateRecord: UpdateRecord
        });
    });
})(SUGAR.App);
